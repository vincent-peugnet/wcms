<?php

namespace Wcms;

use DomainException;

use function Clue\StreamFilter\fun;

/**
 * Identify a font by family, style, weight, stretch,
 * but that may be in multiple file format.
 */
class Font
{
    protected string $family;
    protected ?string $style = null;
    protected ?string $weight = null;
    protected ?string $stretch = null;

    /** @var Media[] $medias */
    protected array $medias;

    public const STYLE = "style";
    public const WEIGHT = "weight";
    public const STRETCH = "stretch";

    public const FONT_OPTIONS = [
        self::STYLE,
        self::WEIGHT,
        self::STRETCH,
    ];

    public const OPTIONS = [
        "italic" => self::STYLE,
        "oblique" => self::STYLE,
        "thin" => self::WEIGHT,
        "extra-light" => self::WEIGHT,
        "light" => self::WEIGHT,
        "medium" => self::WEIGHT,
        "semi-bold" => self::WEIGHT,
        "bold" => self::WEIGHT,
        "extra-bold" => self::WEIGHT,
        "black" => self::WEIGHT,
        "ultra-condensed" => self::STRETCH,
        "extra-condensed" => self::STRETCH,
        "condensed" => self::STRETCH,
        "semi-condensed" => self::STRETCH,
        "semi-expanded" => self::STRETCH,
        "expanded" => self::STRETCH,
        "extra-expanded" => self::STRETCH,
        "ultra-expanded" => self::STRETCH,
    ];

    public const WEIGHT_EQUIV = [
        'thin' => 100,
        'extra-light' => 200,
        'light' => 300,
        'medium' => 500,
        'semi-bold' => 600,
        'extra-bold' => 800,
        'black' => 900,
    ];

    /**
     * Feed it with media sharing same basename. Only font formats may differ.
     *
     * @param Media[] $medias
     */
    public function __construct(array $medias)
    {
        if (!$this->verify($medias)) {
            throw new DomainException("Not all Media objects given to Font share the same ID");
        }
        $this->medias = $medias;
        $media = $medias[0];
        $parts = explode(".", $media->getbasefilename());
        $this->family = $parts[0];

        $options = array_intersect_key(self::OPTIONS, array_flip($parts));
        $options = array_unique(array_flip($options));

        foreach ($options as $param => $value) {
            if ($param === self::WEIGHT) {
                $value = self::WEIGHT_EQUIV[$value] ?? $value;
            }
            $this->$param = $value;
        };
    }

    /**
     * Verify if every Media objects share the same basename
     *
     * @param Media[] $medias
     *
     * @return bool                         True if valid, otherwise false
     */
    protected function verify(array $medias): bool
    {
        $ids = array_map(function (Media $media) {
            return $media->getbasefilename();
        }, $medias);
        return (count(array_unique($ids)) === 1);
    }

    /**
     * Parse the font into CSS @fontface property
     *
     * @return string                       CSS @Fontface property
     */
    public function fontface(): string
    {
        $family = $this->family();
        $css = "@font-face {\n    font-family: \"$family\";\n    src:\n";
        $srcs = array_map(function (Media $media) {
            $url = $media->getabsolutepath();
            $format = $media->extension();
            $src = "        url(\"$url\")";
            return $src;
        }, $this->medias());
        $css .= implode(",\n", $srcs) . ";";
        if (!is_null($this->style)) {
            $css .= "\n    font-style: $this->style;";
        }
        if (!is_null($this->weight())) {
            $css .= "\n    font-weight: $this->weight;";
        }
        if (!is_null($this->stretch)) {
            $css .= "\n    font-stretch: $this->stretch;";
        }
        return "$css\n}\n\n";
    }

    /**
     * @return string                       CSS code to print as Media code
     */
    public function getcode(): string
    {
        $code = "font-family: $this->family;";
        foreach (self::FONT_OPTIONS as $option) {
            if (!is_null($this->$option)) {
                $value = $this->$option;
                $code .= " font-$option: $value;";
            }
        }
        return $code;
    }

    // _____________________________________ G E T ____________________________________

    public function family(): string
    {
        return $this->family;
    }

    public function style(): ?string
    {
        return $this->style;
    }

    public function weight(): ?string
    {
        return $this->weight;
    }

    public function stretch(): ?string
    {
        return $this->stretch;
    }

    /**
     * @return Media[]
     */
    public function medias(): array
    {
        return $this->medias;
    }
}
