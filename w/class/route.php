<?php

class Route
{
    protected $id = null;
    protected $aff = null;
    protected $action = null;
    protected $redirect = null;

    const AFF = ['read', 'edit', 'admin', 'media'];

    public function __construct($vars)
    {
        $this->hydrate($vars);
    }

    public function hydrate($vars)
    {
        foreach ($vars as $var => $value) {
            $method = 'set' . $var;
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function toarray()
    {
        $array = [];
        if (!empty($this->id)) {
            $array[] = 'art';
        }
        if (!empty($this->aff)) {
            $array[] = 'aff='.$this->aff;
        }
        if (!empty($this->action)) {
            $array[] = 'action=' . $this->action;
        }
        if (!empty($this->redirect)) {
            $array[] = $this->redirect;
        }


        return $array;
    }

    function tostring()
    {
        return implode(' ', $this->toarray());
    }



    public function setid($id)
    {
        $this->id = $id;
    }

    public function setaff($aff)
    {
        $this->aff = $aff;

    }

    public function setaction($action)
    {
        $this->action = $action;
    }

    public function setredirect($redirect)
    {
        $this->redirect = $redirect;
    }

    public function id()
    {
        return $this->id;
    }
}




?>