<?php

class Controlleradmin extends Controller
{

    protected $artmanager;

    public function desktop()
    {
        if($this->user->isadmin()) {
            $this->artmanager = new Modelart();
            $artlist = $this->artmanager->list();
            if(in_array(Config::defaultart(), $artlist)) {
                $defaultartexist = true;
            } else {
                $defaultartexist = true;
            }
            $admin = ['artlist' => $artlist, 'defaultartexist' => $defaultartexist];
            $this->showtemplate('admin', $admin);
        }
    }

    public function update()
    {
        if(!isset($_POST['showeditmenu'])) {
            $_POST['showeditmenu'] = false;
        }
        Config::hydrate($_POST);
        if(Config::savejson() !== false) {
        $this->routedirect('admin');
        } else {
            echo 'Can\'t write config file';
        }
    }





}




?>