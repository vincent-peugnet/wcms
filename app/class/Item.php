<?php

namespace Wcms;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;

abstract class Item
{


    public function hydrate($datas = [])
    {
        $error = 0;
        foreach ($datas as $key => $value) {
            $method = 'set' . $key;

            if (method_exists($this, $method)) {
                if ($this->$method($value) === false) {
                    $error++;
                }
            }
        }
        if ($error > 0) {
            return false;
        } else {
            return true;
        }
    }


    public function dry()
    {
        $array = [];
        foreach (get_object_vars($this) as $var => $value) {
            $array[$var] = $this->$var();
        }
        return $array;
    }


    /**
     * Return any asked vars and their values of an object as associative array
     *
     * @param array $vars list of vars
     * @return array Associative array `$var => $value`
     */
    public function drylist(array $vars): array
    {
        $array = [];
        foreach ($vars as $var) {
            if (property_exists($this, $var)) {
                $array[$var] = $this->$var;
            }
        }
        return $array;
    }
    

    /**
     * Tool for accessing different view of the same DateTimeImmutable var
     *
     * @param string $property DateTimeImmutable var to access
     * @param string $option
     *
     * @return mixed string or false if propriety does not exist
     */
    protected function datetransform(string $property, string $option = 'date')
    {
        if (property_exists($this, $property)) {
            if ($option == 'string') {
                return $this->$property->format(DateTime::ISO8601);
            } elseif ($option == 'date' || $option == 'sort') {
                return $this->$property;
            } elseif ($option == 'hrdi') {
                $now = new DateTimeImmutable("now", timezone_open("Europe/Paris"));
                return hrdi($this->$property->diff($now));
            } elseif ($option == 'pdate') {
                return $this->$property->format('Y-m-d');
            } elseif ($option == 'ptime') {
                return $this->$property->format('H:i');
            } elseif ($option == 'dmy') {
                return $this->$property->format('d/m/Y');
            }
        } else {
            return false;
        }
    }
}
