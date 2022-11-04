<?php

namespace Wcms;

class Servicefont
{
    protected Modelmedia $mediamanager;

    /** @var Font[] $fonts */
    protected array $fonts = [];

    /**
     * This will import all the Media as fonts
     */
    public function __construct(Modelmedia $mediamanager)
    {
        $this->mediamanager = $mediamanager;
        $medias = $this->mediamanager->getlistermedia(Model::FONT_DIR, [Media::FONT]);
        $medias = $this->filterfonts($medias);
        $this->fonts = $this->groupfonts($medias);
    }

    /**
     * Remove all non-font Media from a list of Media objects
     *
     * @param Media[] $medias               List of Media objects
     *
     * @return Media[]                      Filtered list, containting only font files
     */
    protected function filterfonts(array $medias): array
    {
        return array_filter($medias, function (Media $media) {
            return $media->type() === Media::FONT;
        });
    }

    /**
     * This will group the media by filename
     *
     * @param Media[] $medias
     *
     * @return Font[]
     */
    protected function groupfonts(array $medias): array
    {
        $groupedmedias = [];
        foreach ($medias as $media) {
            $groupedmedias[$media->getbasefilename()][] = $media;
        }
        $fonts = [];
        foreach ($groupedmedias as $medias) {
            $fonts[] = new Font($medias);
        }
        return $fonts;
    }

    /**
     * Generate CSS file with @fontface rules according to font folder
     *
     * @throws Filesystemexception
     */
    public function writecss(): void
    {
        $fontcss = $this->css();
        Fs::writefile(Model::FONTS_CSS_FILE, $fontcss, 0664);
    }

    /**
     * Generate CSS file as a string using stored fonts
     *
     * @return string                       CSS file ready to be written somewhere
     */
    protected function css(): string
    {
        $css = "";
        foreach ($this->fonts as $font) {
            $css .= $this->parse($font);
        }
        return $css;
    }

    /**
     * Parse a font into CSS @fontface property
     *
     * @param Font $font                    Font to parse
     * @return string                       CSS @Fontface property
     */
    protected function parse(Font $font): string
    {
        $family = $font->family();
        $css = "@font-face {\n    font-family: \"$family\";\n    src:\n";
        $srcs = array_map(function (Media $media) {
            $url = $media->getabsolutepath();
            $format = $media->extension();
            $src = "        url(\"$url\") format(\"$format\")";
            return $src;
        }, $font->medias());
        $css .= implode(",\n", $srcs) . ";";
        if (!is_null($font->style())) {
            $style = $font->style();
            $css .= "\n    font-style: $style;";
        }
        if (!is_null($font->weight())) {
            $weight = $font->weight();
            $css .= "\n    font-weight: $weight;";
        }
        if (!is_null($font->stretch())) {
            $stretch = $font->stretch();
            $css .= "\n    font-stretch: $stretch;";
        }
        return "$css\n}\n\n";
    }

    /**
     * @return Font[]                       List of Fonts objects
     */
    public function fonts(): array
    {
        return $this->fonts;
    }
}
