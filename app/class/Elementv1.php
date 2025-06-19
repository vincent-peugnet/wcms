<?php

namespace Wcms;

use DomainException;

class Elementv1 extends Element
{
    /** @var string[] */
    protected array $id = [];
    protected string $type;

    /** @var bool Include element with HTML tags */
    protected bool $tag;

    public function __construct(string $pageid, string $type)
    {
        $this->id = [$pageid]; // default source is current page
        $this->tag = Config::htmltag();
        $this->urllinker = Config::urllinker();
        $type = strtolower($type);
        if (in_array($type, Pagev1::HTML_ELEMENTS)) {
            $this->type = $type;
        } else {
            throw new DomainException("invalid element type inclusion: $type");
        }
    }


    // ______________________________________________ G E T ________________________________________________________

    /**
     * @return string[]
     */
    public function id(): array
    {
        return $this->id;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function tag(): bool
    {
        return $this->tag;
    }

    // ______________________________________________ S E T ________________________________________________________

    /**
     * @param string[]|string $sources if provided as string, multiple IDs may be space separated
     */
    public function setid($sources): void
    {
        if (is_string($sources)) {
            $this->id = explode(' ', $sources);
        } elseif (is_array($sources)) {
            $this->id = $sources;
        }
    }

    public function settag(bool $tag): void
    {
        $this->tag = $tag;
    }
}
