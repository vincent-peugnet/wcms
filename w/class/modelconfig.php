<?php

class Modelconfig extends Model
{
    public function readconfig()
	{
		if (file_exists(self::CONFIG_FILE)) {
			$current = file_get_contents(self::CONFIG_FILE);
			$donnees = json_decode($current, true);
			return new Config($donnees);
		} else {
			return 0;
		}

	}

	public function createconfig(array $donnees)
	{
		return new Config($donnees);
	}


	public function savejson(string $json)
	{
		file_put_contents(self::CONFIG_FILE, $json);
	}




}







?>