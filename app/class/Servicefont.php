<?php

namespace Wcms;

class Servicefont
{
    /** @var Font[] $fonts */
    protected array $fonts = [];

    /**
     * This will import all the Media as fonts
     *
     * @param Media[] $medias               Feed it with the Media supposed to be fonts non-font Medias will be filtered
     */
    public function __construct(array $medias)
    {
        $medias = $this->filterfonts($medias);
        $groupedmedias = [];
        foreach ($medias as $media) {
            $groupedmedias[$media->id()][] = $media;
        }
        foreach ($groupedmedias as $medias) {
            $this->fonts[] = new Font($medias);
        }
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
     * Generate CSS file as a string using stored fonts
     *
     * @return string                       CSS file ready to be written somewhere
     */
    public function css(): string
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
            $url = $media->getfullpath();
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
