<?php

class Controllerinfo extends Controller
{

    public function __construct($render) {
        parent::__construct($render);
    }

    public function desktop()
    {
        if($this->user->iseditor()) {
            $this->showtemplate('info', ['version' => getversion()]);
        }
    }



  


}


?>