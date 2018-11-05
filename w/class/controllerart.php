<?php

class Controllerart extends Controllerdb
{
    /** @var Art2 */
    protected $art;
    protected $artmanager;
    protected $renderengine;

    public function __construct($id)
    {
        parent::__construct();


        $this->art = new Art2(['id' => $id]);
        $this->artmanager = new Modelart();
        $this->renderengine = new Modelrender();

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

    public function read()
    {
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

    public function edit()
    {
        if ($this->importart() && $this->user->canedit()) {
            $this->showtemplate('edit', ['art' => $this->art, 'artexist' => true]);
        } else {
            $this->redirect('?id=' . $this->art->id());
        }

    }

    public function log()
    {
        $this->importart();
        var_dump($this->art);
    }

    public function add()
    {
        $this->art->reset();
        $this->artmanager->add($this->art);
        $this->redirect('?id=' . $this->art->id() . '&aff=edit');
    }

    public function delete()
    {
        if ($this->user->canedit() && $this->importart()) {

            if (isset($_POST['deleteconfirm']) && $_POST['deleteconfirm'] == true) {
                $this->artmanager->delete($this->art);
                $this->redirect('?id=' . $this->art->id());
            } else {
                $this->showtemplate('delete', ['art' => $this->art, 'artexist' => true]);
            }
        } else {
            $this->redirect('?id=' . $this->art->id());
        }
    }

    public function update()
    {


        if ($this->importart() && $this->user->canedit()) {
            $this->art->hydrate($_POST);
            $this->art->updateedited();
            $this->artmanager->update($this->art);

        }

        $this->redirect('?id=' . $this->art->id() . '&aff=edit');



    }
}




?>