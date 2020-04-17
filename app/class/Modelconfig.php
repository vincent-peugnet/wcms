<?php

namespace Wcms;

abstract class Modelconfig extends Model
{
    public static function readconfig()
    {
        if (file_exists(self::CONFIG_FILE)) {
            $current = file_get_contents(self::CONFIG_FILE);
            $donnees = json_decode($current, true);
            return new Config($donnees);
        } else {
            return 0;
        }
    }

    public static function createconfig(array $donnees)
    {
        return new Config($donnees);
    }


    public static function savejson(string $json)
    {
        file_put_contents(self::CONFIG_FILE, $json);
    }
}
