<?php

namespace Wcms;

class Colors extends Item
{

    protected $file = 'tagcolors.css';


    protected $rawcss = "";
    protected $tagcolor = [];


    public function __construct(string $file = 'tagcolors.css', array $taglist = [])
    {
        $this->setfile($file);
        if (file_exists($this->file)) {
            $this->rawcss = $this->readcssfile();
            $this->tagcolor = $this->parsetagcss($this->rawcss);
        }
        
        if (!empty($taglist)) {
            $this->tagcolor = $this->removeaddtags($taglist);
            $this->rawcss = $this->tocss($this->tagcolor);
            $this->writecssfile($this->file, $this->rawcss);
        }
    }

    /**
     * Read file containing css
     * @return string raw css or empty string
     */
    public function readcssfile(): string
    {
        $rawcss = file_get_contents($this->file);
        if (is_string($rawcss)) {
            return $rawcss;
        }
        return '';
    }

    /**
     * Check if new tags have been created and generate them a background color
     * @param array $taglist associative array using tag as key
     * @return array associative array of `tag => background-color`
     */
    public function removeaddtags(array $taglist = []): array
    {
        $tagcolor = [];
        foreach ($taglist as $tag => $tagcount) {
            if (key_exists($tag, $this->tagcolor)) {
                $tagcolor[$tag] = $this->tagcolor[$tag];
            } else {
                $tagcolor[$tag] = '#' . dechex(rand(50, 255)) . dechex(rand(50, 255)) . dechex(rand(50, 255));
            }
        }
        return $tagcolor;
    }



    /**
     * Transform a CSS string in a array of datas
     * @param string $rawcss CSS string to parse
     * @return array associative array of `tag => background-color`
     */
    public function parsetagcss(string $rawcss): array
    {
        $pattern = '%.tag\_([a-z0-9\-\_]*)\s*\{\s*background-color:\s*(#[A-Fa-f0-9]{6})\;\s*\}%';
        preg_match_all($pattern, $rawcss, $matches);
        $tagcolor = array_combine($matches[1], $matches[2]);
        if ($tagcolor !== false) {
            return $tagcolor;
        } else {
            return [];
        }
    }

    /**
     * Generate CSS string from datas
     * @param array $tagcolor associative array of `tag => background-color`
     * @return string CSS
     */
    public function tocss(array $tagcolor): string
    {
        $css = "";
        foreach ($tagcolor as $tag => $color) {
            $css .= PHP_EOL  . '.tag_' . $tag . ' { background-color: ' . $color . '; }';
        }
        return $css;
    }

    /**
     * Write css in the file
     * @param string $rawcss
     * @throws \InvalidArgumentException If cant create
     */
    public function writecssfile(string $file, string $rawcss)
    {
        accessfile($file, true);
        if (!file_put_contents($file, $rawcss)) {
            throw new \InvalidArgumentException("cant create file : $this->file", 1);
        }
    }

    /**
     * Update tagcolors based on datas
     * @param array $tagcolor associative array of `tag => background-color`
     */
    public function update(array $tagcolor)
    {
        $this->settagcolor($tagcolor);
        $this->rawcss = $this->tocss($this->tagcolor);
        $this->writecssfile($this->file, $this->rawcss);
    }

    public function htmlcolorpicker(): string
    {
        $html = '<ul>';
        foreach ($this->tagcolor as $tag => $color) {
            $i = '<input type="color" name="tagcolor[' . $tag . ']" value="' . $color . '" id="color_' . $tag . '">';
            $l = '<label for="color_' . $tag . '" >' . $tag . '</label>';
            $html .= '<li>' . $i . $l . '</li>';
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

    /**
     * @throws \InvalidArgumentException If cant access file
     */
    public function setfile(string $path)
    {
        accessfile($path);
        $this->file = $path;
    }

    public function setrawcss($rawcss)
    {
        if (is_string($rawcss)) {
            $this->rawcss = $rawcss;
        }
    }

    public function settagcolor($tagcolor)
    {
        if (is_array($tagcolor)) {
            $this->tagcolor = $tagcolor;
        }
    }
}
