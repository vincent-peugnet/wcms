<?php

class Controlleradmin extends Controller
{

    protected $artmanager;
    protected $mediamanager;

    public function desktop()
    {
        if($this->user->isadmin()) {
            $this->artmanager = new Modelart();
            $artlist = $this->artmanager->list();
            $this->mediamanager = new Modelmedia();
            $faviconlist = $this->mediamanager->listfavicon();
            if(in_array(Config::defaultart(), $artlist)) {
                $defaultartexist = true;
            } else {
                $defaultartexist = true;
            }

            $globalcssfile = Model::GLOBAL_DIR . 'global.css';

            if(is_file($globalcssfile)) {
                $globalcss = file_get_contents($globalcssfile);
            } else {
                $globalcss = "";
            }

            $admin = ['artlist' => $artlist, 'defaultartexist' => $defaultartexist, 'globalcss' => $globalcss, 'faviconlist' => $faviconlist];
            $this->showtemplate('admin', $admin);
        } else {
            $this->routedirect('home');
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