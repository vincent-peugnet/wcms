<?php

namespace Wcms;

class Optlist extends Opt
{
    protected $title = 1;
    protected $description = 0;
    protected $thumbnail = 0;
    protected $date = 0;
    protected $time = 0;
    protected $author = 0;
    protected $style = 'list';

    protected $render;

    public function parsehydrate(string $encoded)
    {
        if (is_string($encoded)) {
            parse_str($encoded, $datas);
            $this->hydrate($datas);
        }
    }

    /**
     * Get the code to insert directly
     */
    public function getcode(): string
    {
        return '%LIST?' . $this->getquery() . '%';
    }


    public function listhtml(array $pagelist, Page $actualpage, Modelrender $render)
    {
        $this->render = $render;

        $li = '';

        foreach ($pagelist as $page) {
            // ================= Class =============
            $classdata = [];
            if ($page->id() === $actualpage->id()) {
                $classdata['actual'] = 'current_page';
            }
            $classdata['secure'] = $page->secure('string');
            $class = ' class="' . implode(' ', $classdata) . '" ';


            // ================ Content

            $content = '';

            $title = '<span class="title">' . $page->title() . '</span>';
            if ($this->description()) {
                $content .= '<span class="description">' . $page->description() . '</span>';
            }
            if ($this->date()) {
                $content .= '<time datetime="' . $page->date('pdate') . '">' . $page->date('pdate') . '</time>' . PHP_EOL;
            }
            if ($this->time()) {
                $content .= '<time datetime="' . $page->date('ptime') . '">' . $page->date('ptime') . '</time>' . PHP_EOL;
            }
            if ($this->author()) {
                $content .=  $page->authors('string') . PHP_EOL;
            }
            if ($this->thumbnail) {
                $content .= '<img class="thumbnail" src="' . Model::thumbnailpath() . $page->thumbnail() . '" alt="' . $page->title() . '">';
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

    public function ul(string $content)
    {
        return '<ul class="pagelist">' . PHP_EOL . $content . PHP_EOL . '</ul>';
    }

    public function li(string $content, string $id)
    {
        return '<li id="' . $id . '">' . PHP_EOL . $content . PHP_EOL . '</li>' . PHP_EOL;
    }

    public function a(string $content, string $class, string $id)
    {
        return '<a ' . $class . ' href="' . $this->render->upage($id) . '">' . $content . '</a>' . PHP_EOL;
    }

    public function spandescription(Page $page)
    {
        if ($this->description) {
            return '<span class="description">' . $page->description() . '</span>';
        } else {
            return '';
        }
    }




    // _______________________________________ G E T _____________________________________


    public function title()
    {
        return $this->title;
    }

    public function description()
    {
        return $this->description;
    }

    public function thumbnail()
    {
        return $this->thumbnail;
    }

    public function date()
    {
        return $this->date;
    }

    public function time()
    {
        return $this->time;
    }

    public function author()
    {
        return $this->author;
    }

    public function style()
    {
        return $this->style;
    }


    // _______________________________________ S E T _____________________________________

    public function settitle($title)
    {
        $this->title = intval($title);
    }

    public function setdescription($description)
    {
        $this->description = intval($description);
    }

    public function setthumbnail($thumbnail)
    {
        $this->thumbnail = intval($thumbnail);
    }

    public function setdate($date)
    {
        $this->date = intval($date);
    }

    public function settime($time)
    {
        $this->time = intval($time);
    }

    public function setauthor($author)
    {
        $this->author = intval($author);
    }

    public function setstyle($style)
    {
        if (is_string($style) && key_exists($style, Model::LIST_STYLES)) {
            $this->style = $style;
        }
    }
}
