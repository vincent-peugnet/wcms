<?php

class Dbitem
{
    public function hydrate(array $datas)
    {
        foreach ($datas as $key => $value) {
            $method = 'set' . $key;

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

    }
}


?>