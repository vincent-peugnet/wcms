<?php

namespace Wcms;

use DomainException;

class Font
{
    protected string $family;
    protected ?string $style;
    protected ?string $weight;
    protected ?string $stretch;

    /** @var Media[] $media */
    protected array $medias;

    protected const STYLE = "style";
    protected const WEIGHT = "weight";
    protected const STRETCH = "stretch";

    protected const OPTIONS = [
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

    /**
     * Feed it with media sharing same ID. Only font formats may differ.
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
        $parts = explode(".", $media->id());
        $this->family = $parts[0];

        $options = array_intersect_key(array_flip($parts), self::OPTIONS);
        $options = array_flip(array_unique($options));

        foreach ($options as $param => $value) {
            $this->$param = $value;
        };
    }

    /**
     * Verify if every Media objects share the same ID
     *
     * @param Media[] $medias
     *
     * @return bool                         True if valid, otherwise false
     */
    protected function verify(array $medias): bool
    {
        $ids = array_map(function (Media $media) {
            return $media->id();
        }, $medias);
        return (count(array_unique($ids)) === 1);
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
