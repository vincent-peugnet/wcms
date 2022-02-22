<?php

namespace Wcms;

class Optrss extends Opt
{
    public function getcode(): string
    {
        return '?' . $this->getquery();
    }
}
