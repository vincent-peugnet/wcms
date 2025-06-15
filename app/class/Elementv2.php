<?php

namespace Wcms;

class Elementv2 extends Element
{
    protected string $id;

    public function __construct(string $pageid, string $fullmatch, string $options)
    {
        $this->urllinker = Config::urllinker();
        $this->fullmatch = $fullmatch;
        $this->id = $pageid;
        $this->options = $options;
        $this->analyse($pageid);
    }

    protected function analyse(string $pageid): void
    {
        parse_str($this->options, $datas);
        $this->hydrate($datas);
    }



    // ______________________________________________ G E T ________________________________________________________

    public function id(): string
    {
        return $this->id;
    }

    public function setid($id): void
    {
        $this->id = $id;
    }
}
