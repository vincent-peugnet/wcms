<?php

namespace Wcms;

abstract class Item
{


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
        foreach ($this as $var => $value) {
            $array[$var] = $this->$var();
        }
        return $array;
    }


	/**
	 * Return any asked vars and their values of an object as associative array
	 * 
	 * @param array $vars list of vars
	 * @return array Associative array `$var => $value`
	 */
	public function drylist(array $vars) : array
	{
		$array = [];
		foreach ($vars as $var) {
			if (property_exists($this, $var))
			$array[$var] = $this->$var;
		}
		return $array;
	}

}


?>