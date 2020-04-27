<?php

namespace Wcms;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use RuntimeException;

abstract class Item
{

    /**
     * Hydrate Object with corresponding `set__VAR__`
     * @param array|object $datas associative array using key as var name or object
     * @param bool $sendexception throw exception if error setting variable
     * @return bool true if no error, otherwise false
     * @throws RuntimeException listing var settings errors
     */
    public function hydrate($datas = [], bool $sendexception = false): bool
    {
        $seterrors = [];
        if (is_array($datas) || is_object($datas)) {
            foreach ($datas as $key => $value) {
                $method = 'set' . $key;
                if (method_exists($this, $method)) {
                    if ($this->$method($value) === false) {
                        $seterrors[] = $key;
                    }
                }
            }
        }
        if (!empty($seterrors)) {
            if ($sendexception) {
                $errors = implode(', ', $seterrors);
                $class = get_class($this);
                throw new RuntimeException("objects vars : $errors can't be set in $class object");
            }
            return false;
        } else {
            return true;
        }
    }

    public function dry()
    {
        $array = [];
        $array = $this->obj2array($this, $array);
        return $array;
    }


    public function obj2array($obj, &$arr)
    {
        if (!is_object($obj) && !is_array($obj)) {
            $arr = $obj;
            return $arr;
        }
        foreach ($obj as $key => $value) {
            if (!empty($value)) {
                $arr[$key] = array();
                $this->obj2array($value, $arr[$key]);
            } else {
                $arr[$key] = $value;
            }
        }
        return $arr;
    }

    public function dryold()
    {
        $array = [];
        foreach (get_object_vars($this) as $var => $value) {
            if (is_object($value) && is_subclass_of($value, get_class($this))) {
                $array[$var] = $value->dry();
            } else {
                if (method_exists($this, $var)) {
                    $array[$var] = $this->$var();
                } else {
                    $array[$var] = $value;
                }
            }
        }
        return get_object_vars($this);
        // return $array;
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
