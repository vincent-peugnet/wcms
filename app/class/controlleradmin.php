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

            $globalcss = file_get_contents(Model::GLOBAL_DIR . 'global.css');

            $admin = ['artlist' => $artlist, 'defaultartexist' => $defaultartexist, 'globalcss' => $globalcss];
            $this->showtemplate('admin', $admin);
        }
    }

    public function update()
    {
        if(!isset($_POST['showeditmenu'])) {
            $_POST['showeditmenu'] = false;
        }
        $globalcss = file_put_contents(Model::GLOBAL_DIR . 'global.css', $_POST['globalcss']);

        Config::hydrate($_POST);
        if(Config::savejson() !== false && $globalcss !== false) {
        $this->routedirect('admin');
        } else {
            echo 'Can\'t write config file or global css file';
        }
    }





}




?>