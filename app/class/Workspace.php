<?php

namespace Wcms;

class Workspace extends Item
{
    protected bool $showeditorleftpanel = true;
    protected bool $showeditorrightpanel = false;
    protected bool $showhomeoptionspanel = false;
    protected bool $showhomebookmarkspanel = true;
    protected bool $showmediaoptionspanel = false;
    protected bool $showmediatreepanel = true;

    protected int $fontsize = 15;
    protected string $mediadisplay = self::LIST;
    protected string $highlighttheme = self::THEME_DEFAULT;

    public const FONTSIZE_MIN = 5;
    public const FONTSIZE_MAX = 99;

    public const LIST = 'list';
    public const GALLERY = 'gallery';
    public const MEDIA_DISPLAY = [self::LIST, self:: GALLERY];

    public const THEME_DEFAULT = 'default';
    public const THEME_MONOKAI = 'monokai';
    public const THEME_NONE = 'none';
    public const THEMES = [self::THEME_DEFAULT, self::THEME_MONOKAI, self::THEME_NONE];

    /**
     * @param mixed[] $datas
     */
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

    public function showhomeoptionspanel(): bool
    {
        return $this->showhomeoptionspanel;
    }

    public function showhomebookmarkspanel(): bool
    {
        return $this->showhomebookmarkspanel;
    }

    public function showmediaoptionspanel(): bool
    {
        return $this->showmediaoptionspanel;
    }

    public function showmediatreepanel(): bool
    {
        return $this->showmediatreepanel;
    }

    public function fontsize(): int
    {
        return $this->fontsize;
    }

    public function mediadisplay(): string
    {
        return $this->mediadisplay;
    }

    public function highlighttheme(): string
    {
        return $this->highlighttheme;
    }

    public function setshoweditorleftpanel($show): void
    {
        $this->showeditorleftpanel = boolval($show);
    }

    public function setshoweditorrightpanel($show): void
    {
        $this->showeditorrightpanel = boolval($show);
    }

    public function setshowhomeoptionspanel($show): void
    {
        $this->showhomeoptionspanel = boolval($show);
    }

    public function setshowhomebookmarkspanel($show): void
    {
        $this->showhomebookmarkspanel = boolval($show);
    }

    public function setshowmediaoptionspanel($show): void
    {
        $this->showmediaoptionspanel = boolval($show);
    }

    public function setshowmediatreepanel($show): void
    {
        $this->showmediatreepanel = boolval($show);
    }

    public function setfontsize($fontsize): void
    {
        $fontsize = intval($fontsize);
        if ($fontsize >= self::FONTSIZE_MIN && $fontsize <= self::FONTSIZE_MAX) {
            $this->fontsize = $fontsize;
        }
    }

    public function setmediadisplay(string $mediadisplay): void
    {
        if (in_array($mediadisplay, self::MEDIA_DISPLAY)) {
            $this->mediadisplay = $mediadisplay;
        }
    }

    public function sethighlighttheme(string $theme): void
    {
        if (in_array($theme, self::THEMES)) {
            $this->highlighttheme = $theme;
        }
    }
}
