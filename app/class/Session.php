<?php

namespace Wcms;

use RuntimeException;

class Session extends Item
{
    public $visitor = false;
    public $user = '';
    public $showeditorleftpanel = true;
    public $showeditorrightpanel = false;
    public $homedisplay = 'list';
    public $mediadisplay = 'list';
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

    public function setshoweditorleftpanel($showeditorleftpanel)
    {
        $this->showeditorleftpanel = boolval($showeditorleftpanel);
    }

    public function setshoweditorrightpanel($showeditorrightpanel)
    {
        $this->showeditorrightpanel = boolval($showeditorrightpanel);
    }

    public function sethomedisplay($homedisplay)
    {
        if (in_array($homedisplay, ['list', 'map'])) {
            $this->homedisplay = $homedisplay;
        }
    }

    public function setmediadisplay($mediadisplay)
    {
        if (in_array($mediadisplay, ['list', 'gallery'])) {
            $this->mediadisplay = $mediadisplay;
        }
    }

    public function setwsession($wsession)
    {
        $this->wsession = $wsession;
    }
}
