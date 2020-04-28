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
     * @return bool true if no error, otherwise false
     */
    public function hydrate($datas = []): bool
    {
        $errors = $this->hydratejob($datas);
        if (in_array(false, $errors)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Hydrate Object with corresponding `set__VAR__`
     * @param array|object $datas associative array using key as var name or object
     * @throws RuntimeException listing var settings errors
     */
    public function hydrateexception($datas = [])
    {
        $errors = $this->hydratejob($datas);
        if (!empty($errors)) {
            $errors = implode(', ', $errors);
            $class = get_class($this);
            throw new RuntimeException("objects vars : $errors can't be set in $class object");
        }
    }

    /**
     * Concrete action of hydrate
     * @param mixed $datas
     * @return array associative array where key are methods and value is bool
     */
    public function hydratejob($datas = []): array
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
        return $seterrors;
    }

    /**
     * Export whole object
     * @return array Associative array
     */
    public function dry(): array
    {
        $array = [];
        $array = $this->obj2array($this, $array);
        return $array;
    }

    /**
     * Reccursive transform obj vars to array
     */
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
