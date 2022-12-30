<?php

namespace Wcms;

/**
 * Class used to manage user session
 */
class Session extends Item
{
    public $visitor = false;
    public $user = '';
    public $wsession = '';

    public function __construct($datas = [])
    {
        $this->hydrate($datas);
    }

    public function writesession()
    {
        $_SESSION['user' . Config::basepath()] = $this->dry();
    }

    /**
     * Ajust a session variable
     * @param string $var
     * @param mixed $value
     * @return bool if var exist
     */
    public function addtosession(string $var, $value): bool
    {
        $method = 'set' . $var;
        if (method_exists($this, $method)) {
            $this->$method($value);
            $_SESSION['user' . Config::basepath()][$var] = $this->$var;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Empty current user session
     */
    public function empty(): void
    {
        $_SESSION['user' . Config::basepath()] = [];
    }

    // _________________________ S E T ________________________

    public function setvisitor($visitor)
    {
        $this->visitor = boolval($visitor);
    }

    public function setuser($id)
    {
        if (is_string($id)) {
            $this->user = strip_tags($id);
        }
    }

    public function setwsession($wsession)
    {
        $this->wsession = $wsession;
    }
}
