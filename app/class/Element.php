<?php

namespace Wcms;

/**
 * HTML Element used in pages
 */
abstract class Element extends Item
{
    protected string $fullmatch;
    protected string $options;
    protected int $everylink = 0;
    protected bool $markdown = true;
    protected string $content = '';
    protected int $minheaderid = 1;
    protected int $maxheaderid = 6;
    protected string $headerid = '1-6';
    /** @var bool default value is set in Config class */
    protected bool $urllinker;
    protected int $headeranchor = self::NO_HEADER_ANCHOR;


    public const NO_HEADER_ANCHOR = 0;
    public const HEADER_ANCHOR_LINK = 1;
    public const HEADER_ANCHOR_HASH = 2;
    public const HEADER_ANCHOR_MODES = [
        self::NO_HEADER_ANCHOR, self::HEADER_ANCHOR_LINK, self::HEADER_ANCHOR_HASH
    ];

    // ______________________________________________ F U N ________________________________________________________



    abstract protected function analyse(string $pageid): void;


    // ______________________________________________ G E T ________________________________________________________


    public function fullmatch(): string
    {
        return $this->fullmatch;
    }

    public function options(): string
    {
        return $this->options;
    }

    public function everylink(): int
    {
        return $this->everylink;
    }

    public function markdown(): bool
    {
        return $this->markdown;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function minheaderid(): int
    {
        return $this->minheaderid;
    }

    public function maxheaderid(): int
    {
        return $this->maxheaderid;
    }

    public function headerid(): string
    {
        return $this->headerid;
    }

    public function headeranchor(): int
    {
        return $this->headeranchor;
    }

    public function urllinker(): bool
    {
        return $this->urllinker;
    }






    // ______________________________________________ S E T ________________________________________________________


    public function setfullmatch(string $fullmatch): void
    {
        $this->fullmatch = $fullmatch;
    }

    public function setoptions(string $options): void
    {
        if (!empty($options)) {
            $this->options = $options;
        }
    }

    public function seteverylink(int $level): bool
    {
        if ($level >= 0 && $level <= 16) {
            $this->everylink = $level;
            return true;
        } else {
            return false;
        }
    }

    public function setmarkdown(bool $markdown): void
    {
        $this->markdown = $markdown;
    }

    public function setcontent(string $content): void
    {
        $this->content = $content;
    }

    public function setheaderid(string $headerid): void
    {
        if ($headerid === '0') {
            $this->headerid = '0';
        } else {
            preg_match('~([1-6])\-([1-6])~', $headerid, $out);
            $this->minheaderid = intval($out[1]);
            $this->maxheaderid = intval($out[2]);
        }
    }

    public function setheaderanchor(int $headeranchor): void
    {
        if (in_array($headeranchor, self::HEADER_ANCHOR_MODES)) {
            $this->headeranchor = $headeranchor;
        }
    }

    public function seturllinker(bool $urllinker): void
    {
        $this->urllinker = $urllinker;
    }
}
