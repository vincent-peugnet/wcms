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
}



?>