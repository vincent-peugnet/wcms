<?php

namespace Wcms;

class Colors
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
                $tagcolor[$tag] = '#' . dechex(rand(100, 255)) . dechex(rand(100, 255)) . dechex(rand(100, 255));
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

    public function htmlcolorpicker(array $csstagcolor): string
    {
        $html = '<ul>';
        foreach ($csstagcolor as $tag => $color) {
            $html .= PHP_EOL . '<li><input type="color" name="colors[' . $tag . ']" value="' . $color . '"></li>';
        }
        $html .= PHP_EOL . '</ul>';
        return $html;
    }
}
