<?php

class Controllerart extends Controller
{
    /** @var Art2 */
    protected $art;
    protected $artmanager;
    protected $renderengine;
    protected $fontmanager;

    public function __construct($router)
    {
        parent::__construct($router);

        $this->artmanager = new Modelart();
        $this->renderengine = new Modelrender($router);
        $this->fontmanager = new Modelfont();

    }

    public function setart(string $id, string $route)
    {
        $cleanid = idclean($id);
        if($cleanid !== $id) {
            $this->routedirect($route, ['art' => $cleanid]);
        }
        $this->art = new Art2(['id' => $cleanid]);
    }

    public function importart()
    {
        $art = $this->artmanager->get($this->art);
        if ($art !== false) {
            $this->art = $art;
            //$this->art->autotaglistupdate($this->artmanager->taglist($this->artmanager->getlister(['id', 'title', 'description', 'tag']), $this->art->autotaglist()));
            return true;
        } else {
            return false;
        }
    }

    public function read($id)
    {
        $this->setart($id, 'artread/');
        $now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));


        $artexist = $this->importart();
        $canread = $this->user->level() >= $this->art->secure();
        $cancreate = $this->user->cancreate();
        $alerts = ['alertnotexist' => 'This page does not exist yet', 'alertprivate' => 'You cannot see this page'];
        $body = '';
        $head = '';



        if ($artexist) {

            if ($this->art->daterender() < $this->art->datemodif()) {
                $body = $this->renderengine->renderbody($this->art);
                $this->art->setrender($body);
                $this->art->setdaterender($now);
                $this->artmanager->update($this->art);
            } else {
                $body = $this->art->render();
            }

            $head = $this->renderengine->renderhead($this->art);

            $this->art->addaffcount();
            $this->artmanager->update($this->art);

        }


        $data = array_merge($alerts, ['art' => $this->art, 'artexist' => $artexist, 'canread' => $canread, 'cancreate' => $cancreate, 'readernav' => true, 'body' => $body, 'head' => $head]);


        $this->showtemplate('read', $data);



    }

    public function edit($id)
    {
        $this->setart($id, 'artedit');


        if ($this->importart() && $this->user->canedit()) {
            $tablist = ['section' => $this->art->md(), 'css' => $this->art->css(), 'header' => $this->art->header(), 'nav' => $this->art->nav(), 'aside' => $this->art->aside(), 'footer' => $this->art->footer(), 'html' => $this->art->html(), 'javascript' => $this->art->javascript()];

            $artlist = $this->artmanager->list();

            if (isset($_SESSION['workspace'])) {
                $showleftpanel = $_SESSION['workspace']['showleftpanel'];
                $showrightpanel = $_SESSION['workspace']['showrightpanel'];
            } else {
                $showleftpanel = false;
                $showrightpanel = false;
            }
            $fonts = [];

            $this->showtemplate('edit', ['art' => $this->art, 'artexist' => true, 'tablist' => $tablist, 'artlist' => $artlist, 'showleftpanel' => $showleftpanel, 'showrightpanel' => $showrightpanel, 'fonts' => $fonts]);
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
        if ($this->user->canedit() && !$this->importart()) {
            $this->art->reset();
            $this->artmanager->add($this->art);
            $this->routedirect('artedit', ['art' => $this->art->id()]);
        } else {
            $this->routedirect('artread/', ['art' => $this->art->id()]);
        }
    }

    public function confirmdelete($id)
    {
        $this->setart($id, 'artconfirmdelete');
        if ($this->user->canedit() && $this->importart()) {

            $this->showtemplate('confirmdelete', ['art' => $this->art, 'artexist' => true]);

        } else {
            $this->routedirect('artread/', ['art' => $this->art->id()]);
        }
    }

    public function delete($id)
    {
        $this->setart($id, 'artdelete');
        if ($this->user->canedit() && $this->importart()) {

            $this->artmanager->delete($this->art);
        }
        $this->routedirect('backrouter');
    }

    public function update($id)
    {
        $this->setart($id, 'artupdate');
        $_SESSION['workspace']['showrightpanel'] = isset($_POST['workspace']['showrightpanel']);
        $_SESSION['workspace']['showleftpanel'] = isset($_POST['workspace']['showleftpanel']);

        if ($this->importart() && $this->user->canedit()) {
            $this->art->hydrate($_POST);
            $this->art->updateedited();
            $this->artmanager->update($this->art);

        }

        $this->routedirect('artupdate', ['art' => $this->art->id()]);



    }

    public function artdirect($id)
    {
        $this->routedirect('artread/', ['art' => idclean($id)]);
    }
}




?>