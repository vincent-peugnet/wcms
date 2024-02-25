<?php

namespace Wcms;

use DomainException;
use RangeException;

class Color
{
    public int $r;
    public int $g;
    public int $b;

    /**
     * @param int $r                        red value, stored from 0 to 255
     * @param int $g                        green value, stored from 0 to 255
     * @param int $b                        blue value, stored from 0 to 255
     *
     * @throws DomainException              If RGB values are not between 0 and 255.
     */
    public function __construct(int $r, int $g, int $b)
    {
        if ($r > 255 || $r < 0 || $g > 255 || $g < 0 || $b > 255 || $b < 0) {
            throw new DomainException("Invalid rgb value: $r, $g, $b to define Color object");
        }
        $this->r = $r;
        $this->g = $g;
        $this->b = $b;
    }

    /**
     * @return int                          Luma value from 0 to 255
     */
    public function luma(): int
    {
        return ($this->r * 299 + $this->g * 587 + $this->b * 114) / 1000;
    }

    /**
     * @return string                       hexa color code starting with #
     */
    public function hexa(): string
    {
        $hex = '#';
        $hex .= str_pad(dechex($this->r), 2, '0', STR_PAD_LEFT);
        $hex .= str_pad(dechex($this->g), 2, '0', STR_PAD_LEFT);
        $hex .= str_pad(dechex($this->b), 2, '0', STR_PAD_LEFT);
        return $hex;
    }
}
