<?php

class Modelfont extends Model
{

    const FONT_TYPES = ['woff2', 'woff', 'otf', 'ttf', 'eot', 'svg'];


    public function getfontlist()
    {
        return $this->fontlist($this->list());
    }

    public function renderfontface()
    {
        $list = $this->list();
        $fontlist = $this->fontlist($list);
        $fontface = $this->fontface($fontlist);
        $this->write($fontface);
    }


    public function list()
    {
        if ($handle = opendir(Model::FONT_DIR)) {
            $list = [];
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {

                    $list[] = $entry;

                }
            }
        }

        return $list;

    }

    public function fontlist(array $list)
    {
        $fontlist = [];
        $fonttypes = implode('|', $this::FONT_TYPES);
        $regex = '#(.+)\.('.$fonttypes.')#';
        foreach ($list as $font) {
            if(preg_match($regex, $font, $out)) {
                $fontlist[] = ['id' => $out[1], 'ext' =>$out[2]];
            }
        }

        return $fontlist;
    }

    public function fontface(array $fontlist)
    {
        $fontface = '';
        foreach ($fontlist as $font) {
            $fontface .= '@font-face {' . PHP_EOL . 'font-family: ' . $font['id'] . ';' . PHP_EOL . ' src: url( ' . Model::fontpath() . $font['id'] .'.'. $font['ext'] . ');' . PHP_EOL . '}' . PHP_EOL . PHP_EOL;
        }
        return $fontface;
    }


    public function write(string $fontface)
    {
        $write = file_put_contents(Model::GLOBAL_DIR.'fonts.css', $fontface);
        if($write !== false) {

        }
    }
}









?>