<?php

namespace Wcms;

class Controlleradmin extends Controller
{

    /** @var Modelmedia $mediamanager */
    protected $mediamanager;

    public function desktop()
    {
        if($this->user->isadmin()) {
            $datas['pagelist'] = $this->pagemanager->list();
            $this->mediamanager = new Modelmedia();
            $datas['faviconlist'] = $this->mediamanager->listfavicon();
            $datas['thumbnaillist'] = $this->mediamanager->listthumbnail();
            $datas['interfacecsslist'] = $this->mediamanager->listinterfacecss();
            if(in_array(Config::defaultpage(), $datas['pagelist'])) {
                $datas['defaultpageexist'] = true;
            } else {
                $datas['defaultpageexist'] = false;
            }

            $globalcssfile = Model::GLOBAL_DIR . 'global.css';

            if(is_file($globalcssfile)) {
                $datas['globalcss'] = file_get_contents($globalcssfile);
            } else {
                $datas['globalcss'] = "";
            }

            $this->showtemplate('admin', $datas);
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