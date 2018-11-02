<?php

class Controllerconnect extends Controller
{

    public function desktop()
    {
        $this->showtemplate('connect', ['user' => $this->user]);
    }

}






?>