<?php

namespace Wcms;

use AltoRouter;
use Exception;
use LogicException;

class Optmap extends Optcode
{
    /**
     * Get the code to insert directly
     */
    public function getcode(): string
    {
        return '%MAP' . $this->getquery() . '%';
    }
}
