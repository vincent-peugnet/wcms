<?php

namespace Wcms;

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
        accessfile(Model::GLOBAL_CSS_FILE, true);

        $globalcss = file_put_contents(Model::GLOBAL_CSS_FILE, $_POST['globalcss']);

        Config::hydrate($_POST);
        if (Config::savejson() !== false && $globalcss !== false) {
            $this->routedirect('admin');
        } else {
            echo 'Can\'t write config file or global css file';
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
                        Config::savejson();
                    }
                    break;
            }
        }
        $this->routedirect('admin');
    }
}
