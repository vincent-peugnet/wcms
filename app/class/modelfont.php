<?php

class Modelfont extends Model
{
    public function list()
    {
        if ($handle = opendir(Model::fontpath())) {
            $list = [];
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {

                    $list[] = $entry;

                }
            }
        }

        return $list;

    }
}









?>