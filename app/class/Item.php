<?php

namespace Wcms;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use RuntimeException;

abstract class Item
{
    /** DateTime format used by HTML datetime-local input  */
    protected const HTML_DATETIME_LOCAL = "Y-m-d\TH:i";
    /** Length of short text such as Page title, description, etc... */
    public const LENGTH_SHORT_TEXT = 255;
    /** Length of long text such as Page elements */
    public const LENGTH_LONG_TEXT = 2 ** 20;

    /**
     * Hydrate Object with corresponding `set__VAR__`
     * @param array<string, mixed>|object $datas associative array using key as var name or object
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
     * @param array<string, mixed>|object $datas associative array using key as var name or object
     * @throws RuntimeException listing var settings errors
     */
    public function hydrateexception($datas = []): void
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
     * @param array<string, mixed>|object $datas
     * @return string[] set mothods names that failed
     */
    public function hydratejob($datas = []): array
    {
        $seterrors = [];
        foreach ($datas as $key => $value) {
            $method = 'set' . $key;
            if (method_exists($this, $method)) {
                if ($this->$method($value) === false) {
                    $seterrors[] = $key;
                }
            }
        }
        return $seterrors;
    }

    /**
     * Export Item Object as an array in order to store it in the database
     *
     * @param string $dateformat        Date formating for DateTime properties {@see Item::datetransform()}
     * @return array<string, mixed>     Item as an associative array
     */
    public function dry(string $dateformat = "string"): array
    {
        $array = [];
        foreach (get_object_vars($this) as $var => $value) {
            if ($value instanceof DateTimeInterface) {
                $array[$var] = $this->$var($dateformat);
            } elseif (is_object($value) && $value instanceof self) {
                $array[$var] = $value->dry($dateformat);
            } elseif (is_array($value)) {
                $array[$var] = $this->aa($value, $dateformat);
            } elseif (method_exists($this, $var)) {
                $array[$var] = $this->$var();
            }
        }
        return $array;
    }

    /**
     * Recursive function used to walk into array in search for Objects to converts
     * Only used with self::dry()
     *
     * @param array<mixed, mixed> $arr  Associative array of datas
     * @param string $dateformat        Date formating for DateTime properties {@see Item::datetransform()}
     *
     * @return array<mixed, mixed>
     */
    private function aa(array $arr, string $dateformat = 'string'): array
    {
        $ret = [];
        foreach ($arr as $key => $value) {
            if (is_object($value) && $value instanceof self) {
                $ret[$key] = $value->dry($dateformat);
            } elseif (is_array($value)) {
                $ret[$key] = $this->aa($value, $dateformat);
            } else {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }

    /**
     * Return any asked vars and their values of an object as associative array
     *
     * @param string[]                  $vars list of vars
     * @param string $dateformat        Date formating for DateTime properties {@see Item::datetransform()}
     * @throws InvalidArgumentException if a listed property does not exist or is an object or array
     * @return array<string, mixed>     Associative array `var => value`
     */
    public function drylist(array $vars, string $dateformat = "string"): array
    {
        $array = [];
        foreach ($vars as $var) {
            if (property_exists($this, $var)) {
                if ($this->$var instanceof DateTimeInterface) {
                    $array[$var] = $this->$var($dateformat);
                } elseif (!is_object($this->$var)) {
                    $array[$var] = $this->$var();
                } else {
                    throw new InvalidArgumentException(
                        "$var property of " . get_class($this) . " should not be used with " . __FUNCTION__ . "()"
                    );
                }
            } else {
                throw new InvalidArgumentException(
                    "$var property does not exist in Object of class " . get_class($this)
                );
            }
        }
        return $array;
    }


    /**
     * Tool for accessing different view of the same DateTimeImmutable var
     *
     * @param string $property DateTimeImmutable var to access
     * @param string $option Can be date|string|hrdi|pdate|ptime|dmy|self::HTML_DATETIME_LOCAL
     * @throws InvalidArgumentException if property does not exist, is'nt date or option is not valid
     *
     * @return DateTimeInterface|string
     */
    protected function datetransform(string $property, string $option = 'date')
    {
        if (!property_exists($this, $property)) {
            throw new InvalidArgumentException("Property $property does not exist in " . get_class($this));
        }
        $property = $this->$property;
        if (!$property instanceof DateTimeInterface) {
            throw new InvalidArgumentException("Property $property is not a date property in " . get_class($this));
        }

        if ($option == 'string') {
            return $property->format(DateTime::RFC3339);
        } elseif ($option == 'date' || $option == 'sort') {
            return $property;
        } elseif ($option == 'hrdi') {
            $now = new DateTimeImmutable("now", timezone_open("Europe/Paris"));
            return hrdi($property->diff($now));
        } elseif ($option == 'pdate') {
            return $property->format('Y-m-d');
        } elseif ($option == 'ptime') {
            return $property->format('H:i');
        } elseif ($option == 'dmy') {
            return $property->format('d/m/Y');
        } elseif ($option == self::HTML_DATETIME_LOCAL) {
            return $property->format(self::HTML_DATETIME_LOCAL);
        } else {
            throw new InvalidArgumentException("$option format for datetransform does not exist");
        }
    }
}
