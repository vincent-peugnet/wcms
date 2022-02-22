<?php

namespace Wcms;

class Optrss extends Opt
{
    public function getcode(): string
    {
        return '?' . $this->getquery();
    }

    public function parsehydrate(string $encoded)
    {
        parse_str(ltrim($encoded, "?"), $datas);
        $this->hydrate($datas);
    }

    /**
     * @param Page[] $pagelist
     */
    public function render(array $pagelist): string
    {
        $atom = "";
        foreach ($pagelist as $page) {
            $atom .= $page->title() . PHP_EOL;
        }
        return $atom;
    }
}
