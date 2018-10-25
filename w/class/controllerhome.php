<?php

class Controllerhome extends Controllerdb
{

    protected $modelhome;

    public function __construct() {
        parent::__construct();
        $this->modelhome = new Modelhome;
    }




    public function desktop()
    {
        echo '<h1>Desktop</h1>';

        echo '<h2>Menu Bar</h2>';

        $this->table2();
    }

    public function table2()
    {
        if ($this->useriseditor()) {
            $datas = $this->modelhome->table2();

            var_dump($datas);
 
        }

    }

    public function massedit()
    {
        echo '<h2>Mass Edit</h2>';

    }







}








?>