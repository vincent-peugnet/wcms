<?php

abstract class Configtest
{
    protected static $info;
    
    public static function setinfo($info)
    {
        self::$info = $info;
    }

    public static function info()
    {
        return self::$info;
    }
}





?>