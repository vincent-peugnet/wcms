<?php

class Optlist extends Opt
{
    private $description = 0;
    private $thumbnail = 0;
    private $date = 0;
    private $author = 0;
    private $style = 0;



    public function parsehydrate(string $encoded)
    {
        if(is_string($encoded)) {
            parse_str($encoded, $datas);
            $this->hydrate($datas);
        }
    }


    /**
     * Get the query as http string
     * 
     * @return string The resulted query
     */
    public function getquery() : string
    {
        $class = get_class_vars(get_class($this));
        $object = get_object_vars($this);
        $class['artvarlist'] = $object['artvarlist'];
        $class['taglist'] = $object['taglist'];
        $class['authorlist'] = $object['authorlist'];
        $query = array_diff_assoc_recursive($object, $class);

        return urldecode(http_build_query($query));
    }

    /**
     * Get the code to insert directly
     */
    public function getcode() : string
    {
        return '%LIST?' . $this->getquery() . '%';
    }



    // _______________________________________ G E T _____________________________________


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

    public function author()
    {
        return $this->author;
    }

    public function style()
    {
        return $this->style;
    }



    // _______________________________________ S E T _____________________________________

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