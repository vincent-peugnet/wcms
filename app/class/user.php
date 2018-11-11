<?php

class User
{
    protected $level = 0;

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

    public function canedit()
    {
        // a modifier en prenant compte du code invitation de l'article
        return $this->level >= Modeluser::EDITOR;
    }

    public function cancreate()
    {
        return $this->level >=Modeluser::EDITOR;
    }

    public function isadmin()
    {
        return $this->level === Modeluser::ADMIN;
    }
}



?>