<?php

namespace Wcms;

class Workspace extends Item
{
    protected bool $showeditorleftpanel = true;
    protected bool $showeditorrightpanel = false;
    protected int $fontsize = 15;
    protected string $mediadisplay = self::LIST;

    public const FONTSIZE_MIN = 5;
    public const FONTSIZE_MAX = 99;

    public const LIST = 'list';
    public const GALLERY = 'gallery';
    public const MEDIA_DISPLAY = [self::LIST, self:: GALLERY];

    public function __construct(array $datas)
    {
        $this->hydrate($datas);
    }

    public function showeditorleftpanel(): bool
    {
        return $this->showeditorleftpanel;
    }

    public function showeditorrightpanel(): bool
    {
        return $this->showeditorrightpanel;
    }

    public function fontsize(): int
    {
        return $this->fontsize;
    }

    public function mediadisplay(): string
    {
        return $this->mediadisplay;
    }

    public function setshoweditorleftpanel($show)
    {
        $this->showeditorleftpanel = boolval($show);
    }

    public function setshoweditorrightpanel($show)
    {
        $this->showeditorrightpanel = boolval($show);
    }

    public function setfontsize($fontsize)
    {
        $fontsize = intval($fontsize);
        if ($fontsize >= self::FONTSIZE_MIN && $fontsize <= self::FONTSIZE_MAX) {
            $this->fontsize = $fontsize;
        }
    }

    public function setmediadisplay($mediadisplay)
    {
        if (in_array($mediadisplay, self::MEDIA_DISPLAY)) {
            $this->mediadisplay = $mediadisplay;
        }
    }
}