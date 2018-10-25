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
}



?>