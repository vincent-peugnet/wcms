<?php

namespace Wcms;

class Graph extends Item
{
    protected bool $showorphans = true;
    protected bool $showredirection = false;
    protected bool $showexternallinks = false;
    protected string $layout = 'euler';

    public const LAYOUTS = [
        'cose' => 'cose',
        'fcose' => 'fcose',
        'cose-bilkent' => 'cose-bilkent',
        'euler' => 'euler',
        'circle' => 'circle',
        'breadthfirst' => 'breadthfirst',
        'concentric' => 'concentric',
        'grid' => 'grid',
        'random' => 'random',
    ];

    /**
     * @param mixed[] $datas
     */
    public function __construct(array $datas = [])
    {
        $this->hydrate($datas);
    }

    public function showorphans(): bool
    {
        return $this->showorphans;
    }

    public function showredirection(): bool
    {
        return $this->showredirection;
    }

    public function showexternallinks(): bool
    {
        return $this->showexternallinks;
    }

    public function layout(): string
    {
        return $this->layout;
    }

    public function setshowredirection(bool $showredirection): void
    {
        $this->showredirection = $showredirection;
    }

    public function setshoworphans(bool $showorphans): void
    {
        $this->showorphans = $showorphans;
    }

    public function setshowexternallinks(bool $showexternallinks): void
    {
        $this->showexternallinks = $showexternallinks;
    }

    public function setlayout(string $layout): void
    {
        if (key_exists($layout, $this::LAYOUTS)) {
            $this->layout = $layout;
        }
    }
}
