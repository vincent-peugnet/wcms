<?php

namespace Wcms;

use FTP\Connection;
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

    public const VISIT_COUNT    = '%VISITCOUNT%';
    public const EDIT_COUNT     = '%EDITCOUNT%';
    public const DISPLAY_COUNT  = '%DISPLAYCOUNT%';
    public const CONNECT        = '%CONNECT%';
    public const USER           = '%USER%';

    public const POST_PROCESS_CODES = [
        self::VISIT_COUNT,
        self::EDIT_COUNT,
        self::DISPLAY_COUNT,
        self::CONNECT,
        self::USER,
    ];

    public function __construct(Page $page, User $user)
    {
        $this->page = $page;
        $this->user = $user;
        $this->action = $page->postprocessaction();
    }

    /**
     * Apply post process to page HTML render
     */
    public function process(string $html): string
    {
        $html = $this->jsvars($html);
        if ($this->action) {
            $html = $this->replace($html);
        }
        return $html;
    }

    /**
     * Inject Javscript vars inside HTML head of the page
     */
    private function jsvars(string $html): string
    {
        try {
            $wobj = $this->wobj($this->page, $this->user);
        } catch (JsonException $e) {
            $wobj = '{}';
        }
        $script = "\n<script>const w = $wobj</script>";
        return insert_after($html, '<head>', $script);
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
            self::VISIT_COUNT   => "<span class=\"counter visitcount\">$visitcount</span>",
            self::EDIT_COUNT    => "<span class=\"counter editcount\">$editcount</span>",
            self::DISPLAY_COUNT => "<span class=\"counter displaycount\">$displaycount</span>",
            self::CONNECT       => $this->connect(),
            self::USER          => $this->user->name(),
        ];
        return strtr($text, $replacements);
    }

    /**
     * Datas about given page and user in JSON
     * To be printed with every pages, so it can be used in JS
     *
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



    /**
     * Generate a login or logout form
     *
     * @return string                       HTML code
     */
    protected function connect(): string
    {

        $form = "<form action=\"!co\" method=\"post\">\n";

        if (!$this->user->isvisitor()) {
            $form .= '<input type="submit" name="log" value="logout">';
        } else {
            $form .= '<input type="text" name="user" id="loginuser" autofocus placeholder="user" required>';
            $form .= '<input type="password" name="pass" id="loginpass" placeholder="password" required>';
            $form .= '<input type="submit" name="log" value="login" id="button">';
        }
        $pageid = $this->page->id();
        $form .= '<input type="hidden" name="route" value="pageread">';
        $form .= "<input type=\"hidden\" name=\"id\" value=\"$pageid\">";
        $form .= '</form>';
        return $form;
    }
}
