<?php

namespace Wcms;

use RuntimeException;

class Controlleradmin extends Controller
{
    /** @var Modelmedia $mediamanager */
    protected $mediamanager;
    /** @var Modeladmin */
    protected $adminmanager;

    public function __construct($router)
    {
        parent::__construct($router);

        $this->adminmanager = new Modeladmin();
    }

    public function desktop()
    {
        if ($this->user->isadmin()) {
            $datas['pagelist'] = $this->pagemanager->list();
            $this->mediamanager = new Modelmedia();
            $datas['faviconlist'] = $this->mediamanager->listfavicon();
            $datas['thumbnaillist'] = $this->mediamanager->listthumbnail();
            $datas['interfacecsslist'] = $this->mediamanager->listinterfacecss();

            $globalcssfile = Model::GLOBAL_CSS_FILE;

            if (is_file($globalcssfile)) {
                $datas['globalcss'] = file_get_contents($globalcssfile);
            } else {
                $datas['globalcss'] = "";
            }

            $datas['pagesdblist'] = $this->adminmanager->pagesdblist();
            $datas['pagesdbtree'] = $this->mediamanager->listdir(Model::PAGES_DIR);

            $this->showtemplate('admin', $datas);
        } else {
            $this->routedirect('home');
        }
    }

    public function update()
    {
        try {
            Fs::accessfile(Model::GLOBAL_CSS_FILE, true);
            Fs::writefile(Model::GLOBAL_CSS_FILE, $_POST['globalcss']);
            Config::hydrate($_POST);
            Config::savejson();
            Model::sendflashmessage("Configuration succesfully updated", Model::FLASH_SUCCESS);
            $this->routedirect('admin');
        } catch (Filesystemexception $e) {
            Model::sendflashmessage("Can't write config file or global css file", Model::FLASH_ERROR);
        }
    }

    public function database()
    {
        if (!empty($_POST['action'])) {
            switch ($_POST['action']) {
                case 'duplicate':
                    if (!empty($_POST['dbsrc']) && !empty($_POST['dbtarget'])) {
                        $this->adminmanager->copydb($_POST['dbsrc'], $_POST['dbtarget']);
                    }
                    break;
                case 'select':
                    if (!empty($_POST['pagetable'])) {
                        Config::hydrate($_POST);
                        try {
                            Config::savejson();
                        } catch (RuntimeException $e) {
                            Model::sendflashmessage(
                                'Cannot update Config file : ' . $e->getMessage(),
                                Model::FLASH_ERROR
                            );
                        }
                    }
                    break;
            }
        }
        $this->routedirect('admin');
    }
}
