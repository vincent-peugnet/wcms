<?php

namespace Wcms;

class Optrandom extends Optcode
{
    /** Page from which the random link exist */
    protected string $origin = "";

    /**
     * Get the code to insert directly
     */
    public function getcode(): string
    {
        return '%RANDOM' . $this->getquery() . '%';
    }

    public function origin(): string
    {
        return $this->origin;
    }

    public function setorigin($origin)
    {
        if (Model::idcheck($origin)) {
            $this->origin = $origin;
        }
    }
}
