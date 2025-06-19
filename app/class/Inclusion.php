<?php

namespace Wcms;

class Inclusion
{
    protected string $fullmatch;
    protected string $type;
    /** @var string $options string encoded assoc array without `?` as first char */
    protected string $options;

    /**
     * wildcard `*` in inclusion options is replaced by current page
     */
    public function __construct(string $fullmatch, string $type, string $options, Page $currentpage)
    {
        $this->fullmatch = $fullmatch;
        $this->type = $type;
        $this->options = str_replace('*', $currentpage->id(), $options);
    }

    /**
     * @return array<string, mixed> decoded options as assoc array
     */
    public function readoptions(): array
    {
        parse_str(htmlspecialchars_decode($this->options), $datas);
        return $datas;
    }

    public function fullmatch(): string
    {
        return $this->fullmatch;
    }

    public function type(): string
    {
        return $this->type;
    }
}
