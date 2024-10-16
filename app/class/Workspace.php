<?php

namespace Wcms;

class Workspace extends Item
{
    protected bool $showeditorleftpanel = true;
    protected bool $showeditorrightpanel = false;
    protected bool $showeoptionspanel = false;
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

    public function showeoptionspanel(): bool
    {
        return $this->showeoptionspanel;
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

    public function setshoweditorleftpanel(mixed $show): void
    {
        $this->showeditorleftpanel = boolval($show);
    }

    public function setshoweditorrightpanel(mixed $show): void
    {
        $this->showeditorrightpanel = boolval($show);
    }

    public function setshoweoptionspanel(mixed $show): void
    {
        $this->showeoptionspanel = boolval($show);
    }

    public function setfontsize(mixed $fontsize): void
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
