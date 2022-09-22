<?php

namespace Wcms;

/**
 * Not used anymore, need refactoring
 * @see https://github.com/vincent-peugnet/wcms/issues/217
 */

class Modelfont extends Model
{
    protected const FONT_TYPES = ['woff2', 'woff', 'otf', 'ttf', 'eot', 'svg'];



    public function getfontlist()
    {
        return $this->fontlist($this->list());
    }

    public function getfonttypes()
    {
        $fonttypes = array_map(function ($ext) {
            return '.' . $ext;
        }, $this::FONT_TYPES);
        return implode(', ', $fonttypes);
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
        $list = [];
        if ($handle = opendir(Model::FONT_DIR)) {
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
        $regex = '#(.+)\.(' . $fonttypes . ')#';
        foreach ($list as $font) {
            if (preg_match($regex, $font, $out)) {
                $fontlist[] = ['id' => $out[1], 'ext' => $out[2], 'size' => filesize(Model::FONT_DIR . $font)];
            }
        }
        return $fontlist;
    }

    public function fontface(array $fontlist)
    {
        $fontface = '';
        foreach ($fontlist as $font) {
            $fontface .= '@font-face {\n
                font-family: ' . $font['id'] . ';\n
                src: url( ' . Model::fontpath() . $font['id'] . '.' . $font['ext'] . ');
                }\n\n';
        }
        return $fontface;
    }


    public function write(string $fontface): bool
    {
        return Fs::writefile(self::ASSETS_CSS_DIR . 'fonts.css', $fontface);
    }

    public function upload(array $file, $maxsize = 2 ** 24, $id = null)
    {
        $message = 'runing';
        if ($file['font']['error'] == 0 && $file['font']['size'] < $maxsize) {
            $infosfichier = pathinfo($file['font']['name']);
            $extension_upload = $infosfichier['extension'];
            $extensions_autorisees = $this::FONT_TYPES;
            if (in_array($extension_upload, $extensions_autorisees)) {
                if (!empty($id)) {
                    $id = strtolower(strip_tags($id));
                    $id = str_replace(' ', '_', $id);
                } else {
                    $id = $infosfichier['filename'];
                }
                if (!file_exists($this::FONT_DIR . $id . '.' . $extension_upload)) {
                    $extension_upload = strtolower($extension_upload);
                    $uploadok = move_uploaded_file(
                        $file['font']['tmp_name'],
                        $this::FONT_DIR . $id . '.' . $extension_upload
                    );
                    if ($uploadok) {
                        $message = true;
                    } else {
                        $message = 'uploaderror';
                    }
                } else {
                    $message = 'filealreadyexist';
                }
            }
        } else {
            $message = 'filetoobig';
        }
        return $message;
    }
}
