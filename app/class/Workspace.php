<?php

namespace Wcms;

class Workspace extends Item
{
    protected bool $showeditorleftpanel = true;
    protected bool $showeditorrightpanel = false;
    protected int $fontsize = 15;

    public const FONTSIZE_MIN = 5;
    public const FONTSIZE_MAX = 99;

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
}
