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
    protected $style = 0;

    /** @var Modelrender Render engine used to generate pages urls */
    protected $render = null;



    public function parsehydrate(string $encoded)
    {
        if(is_string($encoded)) {
            parse_str($encoded, $datas);
            $this->hydrate($datas);
        }
    }

    /**
     * Get the code to insert directly
     */
    public function getcode() : string
    {
        return '%LIST?' . $this->getquery() . '%';
    }


    public function listhtml(array $pagelist)
    {
        if(!empty($this->render)) {
            $content = '<ul class="pagelist">' . PHP_EOL;
            foreach ($pagelist as $page) {
                $content .= '<li>' . PHP_EOL;
                $content .= '<a href="' . $this->render->upage($page->id()) . '">' . $page->title() . '</a>' . PHP_EOL;
                if ($this->description()) {
                    $content .= '<em>' . $page->description() . '</em>' . PHP_EOL;
                }
                if ($this->date()) {
                    $content .= '<code>' . $page->date('pdate') . '</code>' . PHP_EOL;
                }
                if ($this->time()) {
                    $content .= '<code>' . $page->date('ptime') . '</code>' . PHP_EOL;
                }
                if ($this->author()) {
                    $content .=  $page->authors('string') . PHP_EOL;
                }
                $content .= '</li>';
            }
            $content .= '</ul>';

            return $content;
                    
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
        $this->style = intval($style);
    }

    public function setrender($render)
    {
        if(is_a($render, 'Wcms\Modelrender')) {
            $this->render = $render;
        }
    }
}
