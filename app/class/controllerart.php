<?php

class Controllerart extends Controller
{
    /** @var Art2 */
    protected $art;
    protected $artmanager;
    protected $renderengine;
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
        $art = $this->artmanager->get($this->art);
        if ($art !== false) {
            $this->art = $art;
            return true;
        } else {
            return false;
        }
    }


    public function canedit()
    {
        if ($this->user->iseditor()) {
            return true;
        } elseif ($this->user->isinvite()) {
            if (in_array($this->user->id(), $this->art->invites())) {
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
            $this->renderart();
            $this->artmanager->update($this->art);
        }
        $this->routedirect('artread/', ['art' => $this->art->id()]);
    }

    public function renderart()
    {
        $now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));

        $this->renderengine = new Modelrender($this->router);

        $body = $this->renderengine->renderbody($this->art);
        $head = $this->renderengine->renderhead($this->art);
        $this->art->setrenderbody($body);
        $this->art->setrenderhead($head);
        $this->art->setdaterender($now);
        $this->art->setlinkfrom($this->renderengine->linkfrom());
        $this->art->setlinkto($this->renderengine->linkto());

        return ['head' => $head, 'body' => $body];

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
                $page = $this->renderart();
            } else {
                $page = ['head' => $this->art->renderhead(), 'body' => $this->art->renderbody()];
            }
            if($canread) {
                $this->art->addaffcount();
                if($this->user->level() < 2) {
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

            $inviteuserlist = $this->usermanager->getlisterbylevel(2);

            if (isset($_SESSION['workspace'])) {
                $showleftpanel = $_SESSION['workspace']['showleftpanel'];
                $showrightpanel = $_SESSION['workspace']['showrightpanel'];
            } else {
                $showleftpanel = false;
                $showrightpanel = false;
            }
            $fonts = [];

            $this->showtemplate('edit', ['art' => $this->art, 'artexist' => true, 'tablist' => $tablist, 'artlist' => $idlist, 'showleftpanel' => $showleftpanel, 'showrightpanel' => $showrightpanel, 'fonts' => $fonts, 'tagartlist' => $tagartlist, 'lasteditedartlist' => $lasteditedartlist, 'faviconlist' => $faviconlist, 'inviteuserlist' => $inviteuserlist]);
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
        $_SESSION['workspace']['showrightpanel'] = isset($_POST['workspace']['showrightpanel']);
        $_SESSION['workspace']['showleftpanel'] = isset($_POST['workspace']['showleftpanel']);

        if (!empty($_POST['fontsize']) && $_POST['fontsize'] !== Config::fontsize()) {
            Config::setfontsize($_POST['fontsize']);
            Config::savejson();
        }



        $date = new DateTimeImmutable($_POST['pdate'] . $_POST['ptime'], new DateTimeZone('Europe/Paris'));
        $date = ['date' => $date];

        if ($this->importart() && $this->canedit()) {

            $oldart = clone $this->art;
            $this->art->hydrate($_POST);

            if (self::COMBINE) {
                if ($_POST['thisdatemodif'] === $oldart->datemodif('string')) {
                    $compare = $this->artmanager->combine($this->art, $oldart);
                    if (!empty($compare['diff'])) {
                        $this->art->hydrate($compare['mergeart']);
                    }
                }
            }
            $this->art->hydrate($date);
            $this->art->updateedited();
            $this->art->addauthor($this->user->id());
            $this->artmanager->update($this->art);

            $this->routedirect('artedit', ['art' => $this->art->id()]);
            
            //$this->showtemplate('updatemerge', $compare);
        }
    }

    public function artdirect($id)
    {
        $this->routedirect('artread/', ['art' => idclean($id)]);
    }
}




?>