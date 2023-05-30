<?php

namespace Wcms;

class Elementv2 extends Element
{
    protected string $id;

    public function __construct($pageid, string $fullmatch, string $options)
    {
        $this->urllinker = Config::urllinker();
        $this->fullmatch = $fullmatch;
        $this->options = $options;
        $this->analyse($pageid);
    }

    protected function analyse(string $pageid)
    {
        if (!empty($this->options)) {
            parse_str($this->options, $datas);
            $this->hydrate($datas);
        } else {
            $this->id = $pageid;
        }
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
