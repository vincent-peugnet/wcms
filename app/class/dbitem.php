<?php

class Dbitem
{
    public function hydrate($datas)
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
		foreach (get_object_vars($this) as $var => $value) {
			if (in_array($var, $this::VAR_DATE)) {
				$array[$var] = $this->$var('string');
			} else {
				$array[$var] = $this->$var();
			}
		}
        return $array;
    }
}


?>