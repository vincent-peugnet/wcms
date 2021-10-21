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
    protected $minheaderid = 1;
    protected $maxheaderid = 6;
    protected $headerid = '1-6';
    protected bool $headeranchor = false;


    // ______________________________________________ F U N ________________________________________________________



    public function __construct($pageid, $datas = [])
    {
        $this->hydrate($datas);
        $this->analyse($pageid);
    }

    private function analyse(string $pageid)
    {
        if (!empty($this->options)) {
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
        $this->content = "\n<{$this->type()}>\n{$this->content()}\n</{$this->type()}>\n";
    }





    // ______________________________________________ G E T ________________________________________________________


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

    public function minheaderid(): int
    {
        return $this->minheaderid;
    }

    public function maxheaderid(): int
    {
        return $this->maxheaderid;
    }

    public function headerid()
    {
        return $this->headerid;
    }

    public function headeranchor()
    {
        return $this->headeranchor;
    }






    // ______________________________________________ S E T ________________________________________________________


    public function setfullmatch(string $fullmatch)
    {
        $this->fullmatch = $fullmatch;
    }

    public function settype(string $type)
    {
        $type = strtolower($type);
        if (in_array($type, Model::TEXT_ELEMENTS)) {
            $this->type = $type;
        }
    }

    public function setoptions(string $options)
    {
        if (!empty($options)) {
            $this->options = $options;
        }
    }

    public function setautolink(int $level)
    {
        if ($level >= 0 && $level <= 16) {
            $this->autolink = $level;
            return true;
        } else {
            return false;
        }
    }

    public function setmarkdown(int $level)
    {
        if ($level >= 0 && $level <= 1) {
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

    public function setheaderid(string $headerid)
    {
        if ($headerid == 0) {
            $this->headerid = 0;
        } else {
            preg_match('~([1-6])\-([1-6])~', $headerid, $out);
            $this->minheaderid = intval($out[1]);
            $this->maxheaderid = intval($out[2]);
        }
    }

    public function setheaderanchor($headeranchor)
    {
        $this->headeranchor = (bool) $headeranchor;
    }
}
