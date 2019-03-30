<?php

class Controllerart extends Controller
{
    /** @var Art2 */
    protected $art;
    protected $artmanager;
    protected $fontmanager;
    protected $mediamanager;

    const COMBINE = false;

    public function __construct($router)
    {
        parent::__construct($router);

        $this->artmanager = new Modelart();
        $this->fontmanager = new Modelfont();
        $this->mediamanager = new Modelmedia();

    }

    public function setart(string $id, string $route)
    {
        $cleanid = idclean($id);
        if ($cleanid !== $id) {
            $this->routedirect($route, ['art' => $cleanid]);
        } else {
            $this->art = new Art2(['id' => $cleanid]);
        }
    }

    public function importart()
    {
        if (isset($_SESSION['artupdate']) && $_SESSION['artupdate']['id'] == $this->art->id()) {
            $art = new Art2($_SESSION['artupdate']);
            unset($_SESSION['artupdate']);
        } else {
            $art = $this->artmanager->get($this->art);
        }
        if ($art !== false) {
            $this->art = $art;
            return true;
        } else {
            return false;
        }

    }


    public function canedit()
    {
        if ($this->user->issupereditor()) {
            return true;
        } elseif ($this->user->isinvite() || $this->user->iseditor()) {
            if (in_array($this->user->id(), $this->art->authors())) {
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
        $this->setart($id, 'artupdate');

        if ($this->importart() && $this->user->iseditor()) {
            $this->art = $this->renderart($this->art);
            $this->artmanager->update($this->art);
        }
        $this->routedirect('artread/', ['art' => $this->art->id()]);
    }

    /**
     * Render given page
     * 
     * @param Art2 $art input
     * 
     * @return Art2 rendered $art
     */
    public function renderart(Art2 $art) : Art2
    {
        $now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));

        $renderengine = new Modelrender($this->router);

        $body = $renderengine->renderbody($art);
        $head = $renderengine->renderhead($art);
        $art->setrenderbody($body);
        $art->setrenderhead($head);
        $art->setdaterender($now);
        $art->setlinkfrom($renderengine->linkfrom());
        $art->setlinkto($renderengine->linkto());

        return $art;

    }

    public function reccursiverender(Art2 $art)
    {
        $relatedarts = array_diff($art->linkto(), [$art->id()]);
        foreach ($relatedarts as $artid ) {
            $art = $this->artmanager->get($artid);
            if($art !== false) {
                $art = $this->renderart($art);
                $this->artmanager->update($art);
            }
        }
    }


    public function read($id)
    {
        $this->setart($id, 'artread/');

        $artexist = $this->importart();
        $canread = $this->user->level() >= $this->art->secure();
        $alerts = ['alertnotexist' => 'This page does not exist yet', 'alertprivate' => 'You cannot see this page'];
        $page = ['head' => '', 'body' => ''];

        if ($artexist) {

            if ($this->art->daterender() < $this->art->datemodif()) {
                if(Config::reccursiverender()) {
                    $this->reccursiverender($this->art);
                }
                $this->art = $this->renderart($this->art);
            }
            $page = ['head' => $this->art->renderhead(), 'body' => $this->art->renderbody()];
            if ($canread) {
                $this->art->addaffcount();
                if ($this->user->level() < 2) {
                    $this->art->addvisitcount();
                }
            }
            $this->artmanager->update($this->art);
        }
        $data = array_merge($alerts, $page, ['art' => $this->art, 'artexist' => $artexist, 'canread' => $canread, 'readernav' => Config::showeditmenu(), 'canedit' => $this->canedit()]);

        $this->showtemplate('read', $data);

    }

    public function edit($id)
    {
        $this->setart($id, 'artedit');


        if ($this->importart() && $this->canedit()) {
            $tablist = ['main' => $this->art->main(), 'css' => $this->art->css(), 'header' => $this->art->header(), 'nav' => $this->art->nav(), 'aside' => $this->art->aside(), 'footer' => $this->art->footer(), 'body' => $this->art->body(), 'javascript' => $this->art->javascript()];

            $faviconlist = $this->mediamanager->listfavicon();
            $idlist = $this->artmanager->list();


            $artlist = $this->artmanager->getlister();
            $tagartlist = $this->artmanager->tagartlist($this->art->tag('array'), $artlist);
            $lasteditedartlist = $this->artmanager->lasteditedartlist(5, $artlist);

            $editorlist = $this->usermanager->getlisterbylevel(2, '>=');

            if (isset($_SESSION['workspace'])) {
                $showleftpanel = $_SESSION['workspace']['showleftpanel'];
                $showrightpanel = $_SESSION['workspace']['showrightpanel'];
            } else {
                $showleftpanel = false;
                $showrightpanel = false;
            }
            $fonts = [];

            $this->showtemplate('edit', ['art' => $this->art, 'artexist' => true, 'tablist' => $tablist, 'artlist' => $idlist, 'showleftpanel' => $showleftpanel, 'showrightpanel' => $showrightpanel, 'fonts' => $fonts, 'tagartlist' => $tagartlist, 'lasteditedartlist' => $lasteditedartlist, 'faviconlist' => $faviconlist, 'editorlist' => $editorlist]);
        } else {
            $this->routedirect('artread/', ['art' => $this->art->id()]);
        }

    }

    public function log($id)
    {
        $this->setart($id, 'artlog');
        $this->importart();
        var_dump($this->art);
    }

    public function add($id)
    {
        $this->setart($id, 'artadd');
        if ($this->user->iseditor() && !$this->importart()) {
            $this->art->reset();
            if (!empty(Config::defaultart())) {
                $defaultart = $this->artmanager->get(Config::defaultart());
                if ($defaultart !== false) {
                    $defaultbody = $defaultart->body();
                }
            }
            if (empty(Config::defaultart()) || $defaultart === false) {
                $defaultbody = Config::defaultbody();
            }
            $this->art->setbody($defaultbody);
            $this->artmanager->add($this->art);
            $this->routedirect('artedit', ['art' => $this->art->id()]);
        } else {
            $this->routedirect('artread/', ['art' => $this->art->id()]);
        }
    }

    public function confirmdelete($id)
    {
        $this->setart($id, 'artconfirmdelete');
        if ($this->user->iseditor() && $this->importart()) {

            $this->showtemplate('confirmdelete', ['art' => $this->art, 'artexist' => true]);

        } else {
            $this->routedirect('artread/', ['art' => $this->art->id()]);
        }
    }

    public function download($id)
    {
        if($this->user->isadmin()) {

            $file = Model::DATABASE_DIR . Config::arttable() . DIRECTORY_SEPARATOR . $id . '.json';
            
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
            $this->routedirect('artread/', ['art' => $id]);
        }
    }

    /**
     * Import page and save it into the database
     */
    public function upload()
    {
        $art = $this->artmanager->getfromfile();

        if(!empty($_POST['id'])) {
            $art->setid(idclean($_POST['id']));
        }

        if($_POST['datecreation']) {
            $art->setdatecreation($this->now);
        }

        if($art !== false) {            
            if($_POST['erase'] || $this->artmanager->get($art) === false) {
                $this->artmanager->add($art);
            }
        }
        $this->routedirect('home');
    }

    public function delete($id)
    {
        $this->setart($id, 'artdelete');
        if ($this->user->iseditor() && $this->importart()) {

            $this->artmanager->delete($this->art);
        }
        $this->routedirect('home');
    }

    public function update($id)
    {
        $this->setart($id, 'artupdate');

        $this->movepanels();
        $this->fontsize();

        $date = new DateTimeImmutable($_POST['pdate'] . $_POST['ptime'], new DateTimeZone('Europe/Paris'));
        $date = ['date' => $date];

        if ($this->importart()) {
            if ($this->canedit()) {                
            
            // Check if someone esle edited the page during the editing.
                $oldart = clone $this->art;
                $this->art->hydrate($_POST);

                if (self::COMBINE && $_POST['thisdatemodif'] === $oldart->datemodif('string')) {

                }

                $this->art->hydrate($date);
                $this->art->updateedited();
                $this->art->addauthor($this->user->id());
                $this->art->removeeditby($this->user->id());

                // Add thumbnail image file under 1Mo
                $this->mediamanager->simpleupload('thumbnail', Model::THUMBNAIL_DIR . $this->art->id(), 1024*1024, ['jpg', 'jpeg', 'JPG', 'JPEG'], true);


                $this->artmanager->update($this->art);

                $this->routedirect('artedit', ['art' => $this->art->id()]);
                
            //$this->showtemplate('updatemerge', $compare);
            } else {
                // If the editor session finished during the editing, let's try to reconnect to save the editing
                $_SESSION['artupdate'] = $_POST;
                $_SESSION['artupdate']['id'] = $this->art->id();
                $this->routedirect('connect');
            }

        }
        $this->routedirect('art');
    }

    /**
     * This function set the actual editor of the page
     * 
     * @param string $artid as the page id
     */
    public function editby(string $artid)
    {
        $this->art = new Art2(['id' => $artid]);
        if($this->importart($artid)) {
            $this->art->addeditby($this->user->id());
            $this->artmanager->update($this->art);
            echo json_encode(['success' => true]);
        } else {
            $this->error(400);
        }
    }

    /**
     * This function remove the actual editor of the page
     * 
     * @param string $artid as the page id
     */
    public function removeeditby(string $artid)
    {
        $this->art = new Art2(['id' => $artid]);
        if($this->importart($artid)) {
            $this->art->removeeditby($this->user->id());
            $this->artmanager->update($this->art);
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

    public function artdirect($id)
    {
        $this->routedirect('artread/', ['art' => idclean($id)]);
    }
}




?>