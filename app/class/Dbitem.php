<?php

namespace Wcms;

use DateTime;
use DateTimeImmutable;

abstract class Dbitem extends Item
{

    public function dry()
    {
        $array = [];
        foreach ($this as $var => $value) {
            if ($value instanceof DateTime || $value instanceof DateTimeImmutable) {
                $array[$var] = $this->$var('string');
            } else {
                $array[$var] = $this->$var();
            }
        }
        return $array;
    }
}
