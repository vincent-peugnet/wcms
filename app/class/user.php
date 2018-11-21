<?php

class User
{
    protected $level = 0;
    protected $password;

    public function __construct($datas = []) {
        if(!empty($datas)) {
            $this->hydrate($datas);
        }
    }

    public function hydrate(array $datas = [])
	{
		foreach ($datas as $key => $value) {
			$method = 'set' . $key;

			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
    }
    
    public function setlevel($level)
    {
        $this->level = $level;
    }

    public function level()
    {
        return $this->level;
    }

    public function isvisitor()
    {
        return $this->level === Modeluser::FREE;
    }

    public function iseditor()
    {
        return $this->level >= Modeluser::EDITOR;
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