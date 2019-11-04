<?php

namespace Wcms;

class Controlleradmin extends Controller
{

    /** @var Modelmedia $mediamanager */
    protected $mediamanager;

    public function desktop()
    {
        if($this->user->isadmin()) {
            $pagelist = $this->pagemanager->list();
            $this->mediamanager = new Modelmedia();
            $faviconlist = $this->mediamanager->listfavicon();
            $interfacecsslist = $this->mediamanager->listinterfacecss();
            if(in_array(Config::defaultpage(), $pagelist)) {
                $defaultpageexist = true;
            } else {
                $defaultpageexist = true;
            }

            $globalcssfile = Model::GLOBAL_DIR . 'global.css';

            if(is_file($globalcssfile)) {
                $globalcss = file_get_contents($globalcssfile);
            } else {
                $globalcss = "";
            }

            $admin = ['pagelist' => $pagelist, 'defaultpageexist' => $defaultpageexist, 'globalcss' => $globalcss, 'faviconlist' => $faviconlist, 'interfacecsslist' => $interfacecsslist];
            $this->showtemplate('admin', $admin);
        } else {
            $this->routedirect('home');
        }
    }

    public function update()
    {        
        $this->globaldircheck();
 
        $globalcss = file_put_contents(Model::GLOBAL_DIR . 'global.css', $_POST['globalcss']);

        Config::hydrate($_POST);
        if(Config::savejson() !== false && $globalcss !== false) {
        $this->routedirect('admin');
        } else {
            echo 'Can\'t write config file or global css file';
        }
    }


	public function globaldircheck()
	{
		if(!is_dir(Model::GLOBAL_DIR)) {
			return mkdir(Model::GLOBAL_DIR);
		} else {
			return true;
		}
	}


}




?>