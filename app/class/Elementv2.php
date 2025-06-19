<?php

namespace Wcms;

class Elementv2 extends Element
{
    protected string $id;

    public function __construct(string $pageid)
    {
        $this->urllinker = Config::urllinker();
        $this->id = $pageid;
    }



    // ______________________________________________ G E T ________________________________________________________

    public function id(): string
    {
        return $this->id;
    }

    public function setid(string $id): void
    {
        $this->id = $id;
    }
}
