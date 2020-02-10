<?php

namespace Wcms;

use Exception;

class Element extends Item
{
    protected $fullmatch;
    protected $type;
    protected $options;
    protected $sources = [];
    protected $autolink = 0;
    protected $markdown = 1;
    protected $content = '';


    // __________________________________________________ F U N ____________________________________________________________



	public function __construct($datas = [], $pageid)
	{
        $this->hydrate($datas);
        $this->analyse($pageid);
    }

    private function analyse(string $pageid)
    {
        if(!empty($this->options)) {
            $this->options = str_replace('*', $pageid, $this->options);
            parse_str($this->options, $datas);
            if (isset($datas['id'])) {
                $this->sources = explode(' ', $datas['id']);
            } else {
                $this->sources = [$pageid];
            }
            $this->hydrate($datas);
        } else {
            $this->sources = [$pageid];
        }
    }

    public function addtags()
    {
        $this->content = PHP_EOL . '<' . $this->type() . '>' . PHP_EOL . $this->content() . PHP_EOL . '</' . $this->type() . '>' . PHP_EOL;
    }





    // __________________________________________________ G E T ____________________________________________________________


    public function fullmatch()
    {
        return $this->fullmatch;
    }

    public function type()
    {
        return $this->type;
    }

    public function options()
    {
        return $this->options;
    }

    public function sources()
    {
        return $this->sources;
    }

    public function autolink()
    {
        return $this->autolink;
    }

    public function markdown()
    {
        return $this->markdown;
    }

    public function content()
    {
        return $this->content;
    }






    // __________________________________________________ S E T ____________________________________________________________


    public function setfullmatch(string $fullmatch)
    {
        $this->fullmatch = $fullmatch;
    }

    public function settype(string $type)
    {
        $type = strtolower($type);
        if(in_array($type, Model::TEXT_ELEMENTS)) {
            $this->type = $type;
        }
    }

    public function setoptions(string $options)
    {
        if(!empty($options)) {
            $this->options = $options;
        }
    }

    public function setautolink(int $level)
    {
        if($level >= 0 && $level <= 16) {
            $this->autolink = $level;
            return true;
        } else {
            return false;
        }
    }

    public function setmarkdown(int $level)
    {
        if($level >= 0 && $level <= 1) {
            $this->markdown = $level;
            return true;
        } else {
            return false;
        }
    }

    public function setcontent(string $content)
    {
        $this->content = $content;
    }

}




?>