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

    /**
     * Instead of adaptative menu expansion, collapse them all at page load.
     * Usefull for mobile browsing.
     * @var bool $collapsemenu
     **/
    protected bool $collapsemenu = false;

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
    public function __construct(array $datas = [])
    {
        $this->hydrate($datas);
    }

    public function resettomobiledefault(): void
    {
        $this->showeditorleftpanel = false;
        $this->showeditorrightpanel = false;
        $this->showhomeoptionspanel = false;
        $this->showhomebookmarkspanel = false;
        $this->showmediaoptionspanel = false;
        $this->showmediatreepanel = true;
        $this->collapsemenu = true;
        $this->highlighttheme = self::THEME_NONE;
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

    public function collapsemenu(): bool
    {
        return $this->collapsemenu;
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

    public function setshoweditorleftpanel(bool $show): void
    {
        $this->showeditorleftpanel = $show;
    }

    public function setshoweditorrightpanel(bool $show): void
    {
        $this->showeditorrightpanel = $show;
    }

    public function setshowhomeoptionspanel(bool $show): void
    {
        $this->showhomeoptionspanel = $show;
    }

    public function setshowhomebookmarkspanel(bool $show): void
    {
        $this->showhomebookmarkspanel = $show;
    }

    public function setshowmediaoptionspanel(bool $show): void
    {
        $this->showmediaoptionspanel = $show;
    }

    public function setshowmediatreepanel(bool $show): void
    {
        $this->showmediatreepanel = $show;
    }

    public function setcollapsemenu(bool $collapse): void
    {
        $this->collapsemenu = $collapse;
    }

    public function setfontsize(int $fontsize): void
    {
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
