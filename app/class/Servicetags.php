<?php

namespace Wcms;

use Wcms\Exception\Filesystemexception;
use Wcms\Exception\Filesystemexception\Fileexception;
use Wcms\Exception\Filesystemexception\Notfoundexception;

class Servicetags
{
    /**
     * @return string[]                     List of tags
     *
     * @throws Fileexception                If a problem with tag file occured
     */
    public function taglist(): array
    {
        try {
            $tagfile = Fs::readfile(Model::TAGS_FILE);
        } catch (Notfoundexception $e) {
            // This mean the tag file does not exist
            return [];
        }
        $tagdata = json_decode($tagfile, true);
        return array_keys($tagdata);
    }

    /**
     * Update the tag JSON cache file with the last tag list.
     * This also re-generate the CSS file.
     *
     * @param array $tags                   Array of tags as keys
     *
     * @return array[]                      Array of tags as keys and two sub-arrays: background-color and color
     *
     * @throws Filesystemexception           If an error occured with file reading or saving
     */
    public function synctags(array $tags): array
    {
        try {
            $tagfile = Fs::readfile(Model::TAGS_FILE);
            $tagdata = json_decode($tagfile, true);
            $tagdata = array_intersect_key($tagdata, $tags);
            $newtags = array_diff_key($tags, $tagdata);
            if (empty($newtags)) {
                return $tagdata;
            }
        } catch (Notfoundexception $e) {
            // it means tags.json file did not exist, not a big deal
            $newtags = $tags;
            $tagdata = [];
        }
        foreach ($newtags as $tag => $count) {
            $bgcolor = new Color(mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
            $txtcolor = $bgcolor->luma() > 128 ? 'black' : 'white';
            $tagdata[$tag] = [
                'background-color' => $bgcolor->hexa(),
                'color' => $txtcolor
            ];
        }
        Fs::writefile(Model::TAGS_FILE, json_encode($tagdata, JSON_PRETTY_PRINT));
        $this->generatecssfile($tagdata);
        return $tagdata;
    }

    /**
     * update tag colors
     * This also re-generate the CSS file.
     *
     * @throws Filesystemexception          When error occured with writing files
     */
    public function updatecolors($post): void
    {
        $tagdata = [];
        foreach ($post as $tag => $color) {
            $color = sscanf($color, "#%02x%02x%02x");
            $bgcolor = new Color($color[0], $color[1], $color[2]);
            $txtcolor = $bgcolor->luma() > 128 ? 'black' : 'white';
            $tagdata[$tag] = [
                'background-color' => $bgcolor->hexa(),
                'color' => $txtcolor
            ];
        }
        Fs::writefile(Model::TAGS_FILE, json_encode($tagdata, JSON_PRETTY_PRINT));
        $this->generatecssfile($tagdata);
    }

    /**
     * Generate CSS file according to tag datas
     *
     * @param array $tagdata                Array of tags as keys and two sub-arrays: background-color and color
     *
     * @throws Filesystemexception          If an error while saving CSS file
     */
    protected function generatecssfile(array $tagdata): void
    {
        $css = '';
        foreach ($tagdata as $tag => $datas) {
            $bgcolor = $datas['background-color'];
            $color = $datas['color'];
            $css .= "\n.tag_$tag { background-color: $bgcolor; color: $color; }";
        }
        Fs::writefile(Model::COLORS_FILE, $css, 0664);
    }
}
