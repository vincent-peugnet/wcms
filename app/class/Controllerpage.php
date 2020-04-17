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

        if ($pageexist) {
            $canread = $this->user->level() >= $this->page->secure();

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
                sleep($this->page->sleep());
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
            $datas['tablist'] = ['main' => $this->page->main(), 'css' => $this->page->css(), 'header' => $this->page->header(), 'nav' => $this->page->nav(), 'aside' => $this->page->aside(), 'footer' => $this->page->footer(), 'body' => $this->page->body(), 'javascript' => $this->page->javascript()];

            $datas['faviconlist'] = $this->mediamanager->listfavicon();
            $datas['thumbnaillist'] = $this->mediamanager->listthumbnail();
            $datas['pagelist'] = $this->pagemanager->list();


            $pagelist = $this->pagemanager->pagelist();
            $datas['tagpagelist'] = $this->pagemanager->tagpagelist($this->page->tag('array'), $pagelist);
            $datas['lasteditedpagelist'] = $this->pagemanager->lasteditedpagelist(5, $pagelist);

            $datas['editorlist'] = $this->usermanager->getlisterbylevel(2, '>=');

            if (isset($_SESSION['workspace'])) {
                $datas['showleftpanel'] = $_SESSION['workspace']['showleftpanel'];
                $datas['showrightpanel'] = $_SESSION['workspace']['showrightpanel'];
            } else {
                $datas['showleftpanel'] = false;
                $datas['showrightpanel'] = false;
            }
            $datas = array_merge($datas, ['page' => $this->page, 'pageexist' => true, 'user' => $this->user]);
            $this->showtemplate('edit', $datas);
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
            $this->pagemanager->add($this->page);
            $this->routedirect('pageedit', ['page' => $this->page->id()]);
        } else {
            $this->routedirect('pageread/', ['page' => $this->page->id()]);
        }
    }

    public function addascopy(string $id, string $copy)
    {
        $id = idclean($id);
        if($this->copy($copy, $id)) {
            $this->routedirect('pageedit', ['page' => $this->page->id()]);
        } else {
            $this->routedirect('pageread/', ['page' => $id]);
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

            $file = Model::PAGES_DIR . Config::pagetable() . DIRECTORY_SEPARATOR . $id . '.json';
            
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

        if($page !== false) {
        
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
        
            if($_POST['erase'] || $this->pagemanager->get($page) === false) {
                if($this->pagemanager->add($page)) {
                    Model::sendflashmessage('Page successfully uploaded', 'success');
                }
            } else {
                Model::sendflashmessage('Page ID already exist, check remplace if you want to erase it', 'warning');
            }
        } else {
            Model::sendflashmessage('Error while importing page JSON', 'error');
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

    public function duplicate(string $srcid, string $targetid)
    {
        $targetid = idclean($targetid);
        if ($this->copy($srcid, $targetid)) {
            $this->routedirect('pageread/', ['page' => $targetid]);
        } else {
            $this->routedirect('pageread/', ['page' => idclean($srcid)]);

        }
    }

    /**
     * Copy a page to a new ID
     * 
     * @param string $srcid Source page ID
     * @param string $targetid Target page ID
     */
    public function copy(string $srcid, string $targetid)
    {
        if ($this->user->iseditor()) {
            $this->page = $this->pagemanager->get($srcid);
            if($this->page !== false && $this->canedit() && $this->pagemanager->get($targetid) === false) {
                $this->page->setid($targetid);
                $this->page->setdatecreation(true); // Reset date of creation
                $this->page->addauthor($this->user->id());
                $this->pagemanager->add($this->page);
                return true;
            }
        }
        return false;
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

                $this->pagemanager->update($this->page);

                
            //$this->showtemplate('updatemerge', $compare);
            } else {
                // If the editor session finished during the editing, let's try to reconnect to save the editing
                $_SESSION['pageupdate'] = $_POST;
                $_SESSION['pageupdate']['id'] = $this->page->id();
                $this->routedirect('connect');
            }

        }
        $this->routedirect('pageedit', ['page' => $this->page->id()]);
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