<?php

class User
{
    protected $id;
    protected $level = 0;
    protected $signature = '';
    protected $password;
    protected $passwordhashed = false;
    protected $cookie = 0;
    protected $columns = ['title', 'datemodif', 'datecreation', 'secure', 'visitcount'];

    public function __construct($datas = [])
    {
        if (!empty($datas)) {
            $this->hydrate($datas);
        }
    }

    public function hydrate($datas = [])
    {
        foreach ($datas as $key => $value) {
            $method = 'set' . $key;

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function dry()
    {
        $array = [];
        foreach (get_class_vars(__class__) as $var => $value) {
            $array[$var] = $this->$var();
        }
        return $array;
    }


    // _________________________ G E T _______________________

    public function id()
    {
        return $this->id;
    }

    public function level()
    {
        return $this->level;
    }

    public function password($type = 'string')
    {
        if ($type === 'int') {
            return strlen($this->password);
        } elseif ($type = 'string') {
            return $this->password;
        }
    }

    public function signature()
    {
        return $this->signature;
    }

    public function passwordhashed()
    {
        return $this->passwordhashed;
    }

    public function cookie()
    {
        return $this->cookie;
    }

    public function columns()
    {
        return $this->columns;
    }



    // _______________________ S E T _______________________

    public function setid($id)
    {
        $id = idclean($id);
        if (strlen($id) < Model::MAX_ID_LENGTH and is_string($id)) {
            $this->id = $id;
        }
    }

    public function setlevel($level)
    {
        $level = intval($level);
        if ($level >= 0 && $level <= 10) {
            $this->level = $level;
        }
    }

    public function setpassword($password)
    {
        if (!empty($password) && is_string($password)) {
            $this->password = $password;
        }

    }

    public function setsignature(string $signature)
    {
        if (strlen($signature) <= 128) {
            $this->signature = $signature;
        }
    }

    public function setpasswordhashed($passwordhashed)
    {
        $this->passwordhashed = boolval($passwordhashed);

    }

    public function setcookie($cookie)
    {
        $cookie = abs(intval($cookie));
        if($cookie >= 365) {$cookie = 365;}
        $this->cookie = $cookie;
    }

    public function setcolumns($columns)
    {
        if(is_array($columns)) {
            $columns = array_filter(array_intersect(array_unique($columns), Model::COLUMNS));
            $this->columns = $columns;
        }
    }







    public function hashpassword()
    {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->passwordhashed = true;
    }

    public function validpassword()
    {
        if(is_string($this->password)) {
            if(strlen($this->password) >= Model::PASSWORD_MIN_LENGTH && strlen($this->password) <= Model::PASSWORD_MAX_LENGTH) {
                return true;
            }
        }
        return false;
    }



    public function isvisitor()
    {
        return $this->level === Modeluser::FREE;
    }

    public function iseditor()
    {
        return $this->level >= Modeluser::EDITOR;
    }

    public function issupereditor()
    {
        return $this->level >= Modeluser::SUPEREDITOR;
    }

    public function isinvite()
    {
        return $this->level >= Modeluser::INVITE;
    }

    public function isadmin()
    {
        return $this->level === Modeluser::ADMIN;
    }
}



?>