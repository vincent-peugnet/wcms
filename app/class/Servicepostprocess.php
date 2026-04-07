<?php

namespace Wcms;

use JsonException;

/**
 * Used to inject JS vars en some replacement to a page HTML just before reading
 */
class Servicepostprocess
{
    /** @var Page The page that will be post processed */
    protected Page $page;

    /** @var User User reading the page */
    protected User $user;

    /** @var bool Indicate if the page need some specific actions like replacements */
    protected bool $action;

    /** @var string May store an alert that should be displayed */
    protected ?string $alert;

    public const VISIT_COUNT     = '%VISITCOUNT%';
    public const EDIT_COUNT      = '%EDITCOUNT%';
    public const AFF_COUNT       = '%DISPLAYCOUNT%';
    public const DISABLED_IF_VISITOR_MARKER = 'wcms-postprocess-disabled_if_visitor';
    public const DISABLED_IF_USER_MARKER = 'wcms-postprocess-disabled_if_user';

    public const COUNTERS = [
        self::VISIT_COUNT,
        self::EDIT_COUNT,
        self::AFF_COUNT,
    ];

    public function __construct(Page $page, User $user, ?string $alert = null)
    {
        $this->page = $page;
        $this->user = $user;
        $this->action = $page->postprocessaction();
        $this->alert = $alert;
    }

    /**
     * Apply post process to page HTML render
     */
    public function process(string $html): string
    {
        $html = $this->js($html);
        if ($this->action) {
            $html = $this->replace($html);
        }
        return $html;
    }

    /**
     * Inject Javascript vars, and an alert message if needed, inside HTML head of the page
     */
    private function js(string $html): string
    {
        try {
            $wobj = $this->wobj($this->page, $this->user);
        } catch (JsonException $e) {
            $wobj = '{}';
        }
        $o = "\n<script>const w = $wobj</script>";
        if ($this->alert !== null) {
            $alert = addslashes($this->alert);
            // this force the browser to render the page before sending the alert message
            $a = "\n<script>window.onload = function(){setTimeout(function(){alert('$alert');},0)}</script>";
        } else {
            $a = '';
        }
        return insert_after($html, '<head>', $o . $a);
    }

    /**
     * Replace counters by their values
     */
    private function replace(string $text): string
    {
        $visitcount = $this->page->visitcount();
        $editcount = $this->page->editcount();
        $displaycount = $this->page->displaycount();

        $replacements = [
            self::VISIT_COUNT => "<span class=\"counter visitcount\">$visitcount</span>",
            self::EDIT_COUNT => "<span class=\"counter editcount\">$editcount</span>",
            self::AFF_COUNT => "<span class=\"counter displaycount\">$displaycount</span>",
            self::DISABLED_IF_VISITOR_MARKER . '="1"' => $this->user->isvisitor() ? 'disabled' : '',
            self::DISABLED_IF_USER_MARKER . '="1"' => !$this->user->isvisitor() ? 'disabled' : '',
        ];
        return strtr($text, $replacements);
    }

    /**
     * @return string                       JSON encoded w global, pages and user datas
     * @throws JsonException                If JSON encoding failed
     */
    private function wobj(Page $page, User $user): string
    {
        $wdatas = [
            'page' => [
                'id' => $page->id(),
                'title' => $page->title(),
                'description' => $page->description(),
                'secure' => $page->secure(),
            ],
            'domain' => Config::url(),
            'basepath' => Config::basepath(),
            'user' => [
                'id' => $user->id(),
                'level' => $user->level(),
                'name' => $user->name(),
            ]
        ];
        return json_encode($wdatas, JSON_THROW_ON_ERROR);
    }
}
