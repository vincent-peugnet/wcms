<?php

namespace Wcms;

class Colors extends Item
{

    protected $file = MODEL::CSS_DIR . 'tagcolors.css';


    protected $rawcss = "";
    protected $tagcolor = [];



    public function __construct(array $taglist = [])
    {
        if ($this->readcssfile()) {
            $this->parsetagcss();
        }
        if (!empty($taglist)) {
            $this->removeaddtags($taglist);
            $this->tocss();
            $this->writecssfile();
        }
    }

    public function readcssfile(): bool
    {
        if (MODEL::dircheck(MODEL::CSS_DIR) && file_exists($this->file)) {
            $this->rawcss = file_get_contents($this->file);
            return true;
        } else {
            return false;
        }
    }

    public function removeaddtags(array $taglist = [])
    {
        $tagcolor = [];
        foreach ($taglist as $tag => $tagcount) {
            if (key_exists($tag, $this->tagcolor)) {
                $tagcolor[$tag] = $this->tagcolor[$tag];
            } else {
                $tagcolor[$tag] = '#' . dechex(rand(50, 255)) . dechex(rand(50, 255)) . dechex(rand(50, 255));
            }
        }
        $this->tagcolor = $tagcolor;
    }



    /**
     * Transform a CSS string in a array of `tag => background-color`
     * 
     * @return array Ouput array using TAG as key and Hex Color as value
     */
    public function parsetagcss()
    {
        $pattern = '%.tag\_([a-z0-9\-\_]*)\s*\{\s*background-color:\s*(#[A-Fa-f0-9]{6})\;\s*\}%';
        preg_match_all($pattern, $this->rawcss, $matches);
        $tagcolor = array_combine($matches[1], $matches[2]);
        if ($tagcolor !== false) {
            $this->tagcolor = $tagcolor;
            return true;
        } else {
            return false;
        }
    }

    public function tocss()
    {
        $css = "";
        foreach ($this->tagcolor as $tag => $color) {
            $css .= PHP_EOL  . '.tag_' . $tag . ' { background-color: ' . $color . '; }';
        }
        $this->rawcss = $css;
    }

    public function writecssfile()
    {
        if (MODEL::dircheck(MODEL::CSS_DIR)) {
            return file_put_contents($this->file, $this->rawcss);
        }
    }

    public function htmlcolorpicker(): string
    {
        $html = '<ul>';
        foreach ($this->tagcolor as $tag => $color) {
            $html .= PHP_EOL . '<li><input type="color" name="tagcolor[' . $tag . ']" value="' . $color . '" id="color_' . $tag . '"><label for="color_' . $tag . '" >' . $tag . '</label></li>';
        }
        $html .= PHP_EOL . '</ul>';
        return $html;
    }


    // ______________________ G E T _________________________

    public function rawcss()
    {
        return $this->rawcss;
    }

    public function tagcolor()
    {
        return $this->tagcolor;
    }

    // _______________________ S E T _________________________

    public function setrawcss($rawcss)
    {
        if(is_string($rawcss)) {
            $this->rawcss = $rawcss;
        }
    }

    public function settagcolor($tagcolor)
    {
        if(is_array($tagcolor)) {
            $this->tagcolor = $tagcolor;
        }
    }
}
