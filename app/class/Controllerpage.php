<?php

namespace Wcms;

use DateTimeImmutable;
use DateTimeZone;
use Exception;

class Controllerpage extends Controller
{
    /** @var Page */
    protected $page;
    protected $fontmanager;
    protected $mediamanager;

    const COMBINE = false;

    public function __construct($router)
    {
        parent::__construct($router);

        $this->fontmanager = new Modelfont();
        $this->mediamanager = new Modelmedia();

    }

    public function setpage(string $id, string $route)
    {
        $cleanid = idclean($id);
        if ($cleanid !== $id) {
            $this->routedirect($route, ['page' => $cleanid]);
        } else {
            $this->page = new Page(['id' => $cleanid]);
        }
    }

    public function importpage()
    {
        if (isset($_SESSION['pageupdate']) && $_SESSION['pageupdate']['id'] == $this->page->id()) {
            $page = new Page($_SESSION['pageupdate']);
            unset($_SESSION['pageupdate']);
        } else {
            $page = $this->pagemanager->get($this->page);
        }
        if ($page !== false) {
            $this->page = $page;
            return true;
        } else {
            return false;
        }

    }

    /**
     * show credentials for unconnected editors for a specific page
     * 
     * @param string $route direction to redirect after the connection form
     * @return void
     */
    public function pageconnect(string $route)
    {
        if($this->user->isvisitor()) {
            $this->showtemplate('connect', ['route' => $route, 'id' => $this->page->id()]);
            exit;
        }
    }


    public function canedit()
    {
        if ($this->user->issupereditor()) {
            return true;
        } elseif ($this->user->isinvite() || $this->user->iseditor()) {
            if (in_array($this->user->id(), $this->page->authors())) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function render($id)
    {
        $this->setpage($id, 'pageupdate');

        if ($this->importpage() && $this->user->iseditor()) {
            $this->page = $this->renderpage($this->page);
            $this->pagemanager->update($this->page);
        }
        $this->routedirect('pageread/', ['page' => $this->page->id()]);
    }

    /**
     * Render given page
     * 
     * @param Page $page input
     * 
     * @return Page rendered $page
     */
    public function renderpage(Page $page) : Page
    {
        $now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));

        $renderengine = new Modelrender($this->router);

        $renderengine->render($page);
        $page->setdaterender($now);
        $page->setlinkfrom($renderengine->linkfrom());
        $page->setlinkto($renderengine->linkto());

        return $page;

    }

    public function reccursiverender(Page $page)
    {
        $relatedpages = array_diff($page->linkto(), [$page->id()]);
        foreach ($relatedpages as $pageid ) {
            $page = $this->pagemanager->get($pageid);
            if($page !== false) {
                $page = $this->renderpage($page);
                $this->pagemanager->update($page);
            }
        }
    }


    public function read($id)
    {
        $this->setpage($id, 'pageread/');

        $pageexist = $this->importpage();
        $canread = $this->user->level() >= $this->page->secure();
        $page = ['head' => '', 'body' => ''];

        if ($pageexist) {

            if ($this->page->daterender() < $this->page->datemodif()) {
                if(Config::reccursiverender()) {
                    $this->reccursiverender($this->page);
                }
                $this->page = $this->renderpage($this->page);
            }
            if ($canread) {
                $this->page->addaffcount();
                if ($this->user->level() < 2) {
                    $this->page->addvisitcount();
                }
            }
            $this->pagemanager->update($this->page);
        }

        if($pageexist && $canread) {
            $filedir = Model::HTML_RENDER_DIR . $id . '.html';
            if(file_exists($filedir)) {
                $html = file_get_contents($filedir);
                echo $html;
            } else {
                echo 'Please render this page';
            }
        } else {
            $this->showtemplate('alert', ['page' => $this->page, 'pageexist' => $pageexist, 'canedit' => $this->canedit()]);
        }
    }

    public function edit($id)
    {
        $this->setpage($id, 'pageedit');

        $this->pageconnect('pageedit');


        if ($this->importpage() && $this->canedit()) {
            $tablist = ['main' => $this->page->main(), 'css' => $this->page->css(), 'header' => $this->page->header(), 'nav' => $this->page->nav(), 'aside' => $this->page->aside(), 'footer' => $this->page->footer(), 'body' => $this->page->body(), 'javascript' => $this->page->javascript()];

            $faviconlist = $this->mediamanager->listfavicon();
            $idlist = $this->pagemanager->list();


            $pagelist = $this->pagemanager->getlister();
            $tagpagelist = $this->pagemanager->tagpagelist($this->page->tag('array'), $pagelist);
            $lasteditedpagelist = $this->pagemanager->lasteditedpagelist(5, $pagelist);

            $editorlist = $this->usermanager->getlisterbylevel(2, '>=');

            if (isset($_SESSION['workspace'])) {
                $showleftpanel = $_SESSION['workspace']['showleftpanel'];
                $showrightpanel = $_SESSION['workspace']['showrightpanel'];
            } else {
                $showleftpanel = false;
                $showrightpanel = false;
            }
            $fonts = [];

            $this->showtemplate('edit', ['page' => $this->page, 'pageexist' => true, 'tablist' => $tablist, 'pagelist' => $idlist, 'showleftpanel' => $showleftpanel, 'showrightpanel' => $showrightpanel, 'fonts' => $fonts, 'tagpagelist' => $tagpagelist, 'lasteditedpagelist' => $lasteditedpagelist, 'faviconlist' => $faviconlist, 'editorlist' => $editorlist, 'user' => $this->user]);
        } else {
            $this->routedirect('pageread/', ['page' => $this->page->id()]);
        }

    }

    public function log($id)
    {
        if($this->user->issupereditor()) {
            $this->setpage($id, 'pagelog');
            $this->importpage();
            var_dump($this->page);
        } else {
            $this->routedirect('pageread/', ['page' => $id]);
        }
    }

    public function add($id)
    {
        $this->setpage($id, 'pageadd');

        $this->pageconnect('pageadd');

        if ($this->user->iseditor() && !$this->importpage()) {
            $this->page->reset();
            $this->page->addauthor($this->user->id());
            if (!empty(Config::defaultpage())) {
                $defaultpage = $this->pagemanager->get(Config::defaultpage());
                if ($defaultpage !== false) {
                    $defaultbody = $defaultpage->body();
                }
            }
            if (empty(Config::defaultpage()) || $defaultpage === false) {
                $defaultbody = Config::defaultbody();
            }
            $this->page->setbody($defaultbody);
            $this->pagemanager->add($this->page);
            $this->routedirect('pageedit', ['page' => $this->page->id()]);
        } else {
            $this->routedirect('pageread/', ['page' => $this->page->id()]);
        }
    }

    public function confirmdelete($id)
    {
        $this->setpage($id, 'pageconfirmdelete');
        if ($this->importpage() && ($this->user->issupereditor() || $this->page->authors() === [$this->user->id()] )) {

            $this->showtemplate('confirmdelete', ['page' => $this->page, 'pageexist' => true]);

        } else {
            $this->routedirect('pageread/', ['page' => $this->page->id()]);
        }
    }

    public function download($id)
    {
        if($this->user->isadmin()) {

            $file = Model::DATABASE_DIR . Config::pagetable() . DIRECTORY_SEPARATOR . $id . '.json';
            
            if (file_exists($file)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/json; charset=utf-8');
                header('Content-Disposition: attachment; filename="'.basename($file).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                readfile($file);
                exit;
            }
        } else {
            $this->routedirect('pageread/', ['page' => $id]);
        }
    }

    /**
     * Import page and save it into the database
     */
    public function upload()
    {
        $page = $this->pagemanager->getfromfile();

        
        if(!empty($_POST['id'])) {
            $page->setid(idclean($_POST['id']));
        }
        
        if($_POST['datecreation']) {
            $page->setdatecreation($this->now);
        }
        
        if($_POST['author']) {
            $page->setauthors([$this->user->id()]);
        }
        
        $page->setdaterender($page->datecreation('date'));
        
        if($page !== false) {            
            if($_POST['erase'] || $this->pagemanager->get($page) === false) {
                $this->pagemanager->add($page);
            }
        }
        $this->routedirect('home');
    }

    public function delete($id)
    {
        $this->setpage($id, 'pagedelete');
        if ($this->user->iseditor() && $this->importpage()) {

            $this->pagemanager->delete($this->page);
        }
        $this->routedirect('home');
    }

    public function update($id)
    {
        $this->setpage($id, 'pageupdate');

        $this->movepanels();
        $this->fontsize();



        if ($this->importpage()) {
            if ($this->canedit()) {
            
            // Check if someone esle edited the page during the editing.
                $oldpage = clone $this->page;
                $this->page->hydrate($_POST);

                if (self::COMBINE && $_POST['thisdatemodif'] === $oldpage->datemodif('string')) {

                }

                $this->page->updateedited();
                $this->page->addauthor($this->user->id());
                $this->page->removeeditby($this->user->id());

                // Add thumbnail image file under 1Mo
                If(isset($_FILES)) {
                    $this->mediamanager->dircheck(Model::THUMBNAIL_DIR);
                    $this->mediamanager->simpleupload('thumbnail', Model::THUMBNAIL_DIR . $this->page->id(), 1024*1024, ['jpg', 'jpeg', 'JPG', 'JPEG'], true);
                }


                $this->pagemanager->update($this->page);

                $this->routedirect('pageedit', ['page' => $this->page->id()]);
                
            //$this->showtemplate('updatemerge', $compare);
            } else {
                // If the editor session finished during the editing, let's try to reconnect to save the editing
                $_SESSION['pageupdate'] = $_POST;
                $_SESSION['pageupdate']['id'] = $this->page->id();
                $this->routedirect('connect');
            }

        }
        $this->routedirect('page');
    }

    /**
     * This function set the actual editor of the page
     * 
     * @param string $pageid as the page id
     */
    public function editby(string $pageid)
    {
        $this->page = new Page(['id' => $pageid]);
        if($this->importpage($pageid)) {
            $this->page->addeditby($this->user->id());
            $this->pagemanager->update($this->page);
            echo json_encode(['success' => true]);
        } else {
            $this->error(400);
        }
    }

    /**
     * This function remove the actual editor of the page
     * 
     * @param string $pageid as the page id
     */
    public function removeeditby(string $pageid)
    {
        $this->page = new Page(['id' => $pageid]);
        if($this->importpage($pageid)) {
            $this->page->removeeditby($this->user->id());
            $this->pagemanager->update($this->page);
            echo json_encode(['success' => true]);
        } else {
            $this->error(400);
        }
    }


    public function movepanels()
    {
        $_SESSION['workspace']['showrightpanel'] = isset($_POST['workspace']['showrightpanel']);
        $_SESSION['workspace']['showleftpanel'] = isset($_POST['workspace']['showleftpanel']);
    }

    public function fontsize()
    {
        if (!empty($_POST['fontsize']) && $_POST['fontsize'] !== Config::fontsize()) {
            Config::setfontsize($_POST['fontsize']);
            Config::savejson();
        }
    }
    
    public function pagedirect($id)
    {
        $this->routedirect('pageread/', ['page' => idclean($id)]);
    }
}




?>