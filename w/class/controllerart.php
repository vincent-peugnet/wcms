<?php

class Controllerart extends Controllerdb
{
    /** @var Art2 */
    protected $art;


    public function __construct($id) {
        parent::__construct();
    

        $this->art = new Art2(['id' => $id]);
    }

    public function importart()
    {
        if($this->artmanager->exist($this->art->id())) {
            $this->art = $this->artmanager->get($this->art);
            $this->art->autotaglistupdate($this->artmanager->taglist($this->artmanager->getlister(['id', 'title', 'description', 'tag']), $this->art->autotaglist()));

            return true;
        } else {
            return false;
        }
    }

    public function read()
    {

        $artexist = $this->importart();
        $display = $this->user->level() >= $this->art->secure();
        $cancreate = $this->user->cancreate();

        $this->showtemplate('read', ['art' => $this->art, 'artexist' => $artexist, 'display' => $display,  'cancreate' => $cancreate]);                
 


    }

    public function edit()
    {
        if($this->importart() && $this->user->canedit()) {
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
        echo '<h2>Add</h2>';


        $this->art->reset();
        $this->artmanager->add($this->art);
    }

    public function delete()
    {
        echo '<h2>Delete</h2>';
        $this->artmanager->delete($this->art);
    }

    public function update()
    {


        if($this->importart()) {
            $this->art->hydrate($_POST);
        }

        // $this->art->updatelinkfrom();
        // $this->art->autotaglistcalc($this->artmanager->taglist($this->artmanager->getlister(['id', 'title', 'tag']), $this->art->autotaglist()));
        $this->artmanager->update($this->art);

        $this->redirect('?id=' . $this->art->id() . '&aff=edit');

        

    }
}




?>