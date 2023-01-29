<?php

namespace Wcms;

use IntlDateFormatter;
use LogicException;
use Wcms\Exception\Database\Notfoundexception;

class Optlist extends Opt
{
    protected bool $title = true;
    protected bool $description = false;
    protected bool $thumbnail = false;
    protected bool $date = false;
    protected bool $time = false;
    protected bool $author = false;
    protected bool $hidecurrent = false;
    protected $style = 'list';

    /** @var Page $page Current page */
    protected ?Page $page;

    private Servicerender $render;

    /** @var string $bookmark Associated bookmark ID */
    protected string $bookmark = "";

    public function __construct(array $datas = [])
    {
        parent::__construct($datas);
    }

    public function parsehydrate(string $encoded, Page $currentpage)
    {
        $this->page = $currentpage;
        $encoded = str_replace('*', $this->page->id(), $encoded);
        parse_str(ltrim($encoded, "?"), $datas);
        $this->hydrate($datas);
    }

    /**
     * Get the code to insert directly
     */
    public function getcode(): string
    {
        return '%LIST?' . $this->getquery() . '%';
    }

    /**
     * @param Page[] $pagelist              Assoc array of Page objects, key must be ID of page.
     * @param Servicerender $render
     * @return string HTML formated string
     */
    public function listhtml(array $pagelist, Servicerender $render): string
    {
        if (is_null($this->page)) {
            throw new LogicException('A Page object should be given to Optlist constructor to allow HTML rendering');
        };
        $this->render = $render;

        $li = '';

        if ($this->hidecurrent && key_exists($this->page->id(), $pagelist)) {
            unset($pagelist[$this->page->id()]);
        }

        $lang = $this->page->lang() == '' ? Config::lang() : $this->page->lang();
        $dateformatter = new IntlDateFormatter($lang, IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
        $datetitleformatter = new IntlDateFormatter($lang, IntlDateFormatter::FULL, IntlDateFormatter::NONE);
        $timeformatter = new IntlDateFormatter($lang, IntlDateFormatter::NONE, IntlDateFormatter::SHORT);
        foreach ($pagelist as $page) {
            // ================= Class =============
            $classdata = [];
            if ($page->id() === $this->page->id()) {
                $classdata['actual'] = 'current_page';
            }
            $classdata['secure'] = $page->secure('string');
            $class = ' class="' . implode(' ', $classdata) . '" ';


            // ================ Content

            $content = '';

            $title = !$this->description() && $page->ispublic() ? $page->description() : '';
            $title = '<span class="title" title="' . $title . '">' . $page->title() . '</span>';
            if ($this->description()) {
                $content .= '<span class="description">' . $page->description() . '</span>';
            }
            if ($this->date()) {
                $dateattr = $page->date('pdate');
                $date = $dateformatter->format($page->date());
                $datetitle = $datetitleformatter->format($page->date());
                $content .= "<time datetime=\"$dateattr\" title=\"$datetitle\">$date</time>\n";
            }
            if ($this->time()) {
                $timeattr = $page->date('ptime');
                $time = $timeformatter->format($page->date());
                $content .= "<time datetime=\"$timeattr\">$time</time>\n";
            }
            if ($this->author()) {
                $usermanager = new Modeluser();
                foreach ($page->authors() as $author) {
                    try {
                        $user = $usermanager->get($author);
                        $content .= "\n" . $this->render->user($user) . "\n";
                    } catch (Notfoundexception $e) {
                    }
                }
            }
            if ($this->thumbnail) {
                if (!empty($page->thumbnail())) {
                    $src = Model::thumbnailpath() . $page->thumbnail();
                } elseif (!empty(Config::defaultthumbnail())) {
                    $src = Model::thumbnailpath() . Config::defaultthumbnail();
                } else {
                    $src = "";
                }
                $content .= '<img class="thumbnail" src="' . $src . '" alt="' . $page->title() . '">';
            }



            switch ($this->style) {
                case 'card':
                    $li .= $this->li($this->a($title . $content, $class, $page->id()), $page->id());
                    break;

                case 'list':
                    $li .= $this->li($this->a($title, $class, $page->id()) . $content, $page->id());
                    break;
            }
        }

        $html = $this->ul($li);

        return $html;
    }

    private function ul(string $content)
    {
        return "<ul class=\"pagelist\">\n$content\n</ul>\n";
    }

    private function li(string $content, string $class)
    {
        return "<li class=\"pagelistitem $class\">\n$content\n</li>\n";
    }

    private function a(string $content, string $class, string $id)
    {
        return '<a ' . $class . ' href="' . $this->render->upage($id) . '">' . $content . '</a>';
    }




    // _______________________________________ G E T _____________________________________


    public function title(): bool
    {
        return $this->title;
    }

    public function description(): bool
    {
        return $this->description;
    }

    public function thumbnail(): bool
    {
        return $this->thumbnail;
    }

    public function date(): bool
    {
        return $this->date;
    }

    public function time(): bool
    {
        return $this->time;
    }

    public function author(): bool
    {
        return $this->author;
    }

    public function hidecurrent(): bool
    {
        return $this->hidecurrent;
    }

    public function style()
    {
        return $this->style;
    }

    public function bookmark()
    {
        return $this->bookmark;
    }


    // _______________________________________ S E T _____________________________________

    public function settitle($title)
    {
        $this->title = boolval($title);
    }

    public function setdescription($description)
    {
        $this->description = boolval($description);
    }

    public function setthumbnail($thumbnail)
    {
        $this->thumbnail = boolval($thumbnail);
    }

    public function setdate($date)
    {
        $this->date = boolval($date);
    }

    public function settime($time)
    {
        $this->time = boolval($time);
    }

    public function setauthor($author)
    {
        $this->author = boolval($author);
    }

    public function sethidecurrent($hidecurrent)
    {
        $this->hidecurrent = boolval($hidecurrent);
    }

    public function setstyle($style)
    {
        if (is_string($style) && key_exists($style, Model::LIST_STYLES)) {
            $this->style = $style;
        }
    }

    /**
     * @param string $bookmark              Bookmark ID
     */
    public function setbookmark(string $bookmark)
    {
        if (Model::idcheck($bookmark)) {
            $this->bookmark = $bookmark;
        } else {
            $this->bookmark = "";
        }
    }
}
