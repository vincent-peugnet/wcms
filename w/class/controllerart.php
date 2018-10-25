<?php

class Controllerart extends Controllerdb
{
    /** @var Art2 */
    protected $art;


    public function __construct($id) {
        parent::__construct();
    
        
        //echo '<h1>Article</h1>';
        //echo $id;

        $this->art = new Art2(['id' => $id]);
    }

    public function importart()
    {
        if($this->artmanager->exist($this->art->id())) {
            $this->art = $this->artmanager->get($this->art);
            //var_dump($this->art);            
            $this->art->autotaglistupdate($this->artmanager->taglist($this->artmanager->getlister(['id', 'title', 'description', 'tag']), $this->art->autotaglist()));

            return true;
        } else {
            echo '<h3>Article does not exist yet.</h3>';
            return false;
        }
    }

    public function read()
    {

        if($this->importart()) {
            if($this->user->level() >= $this->art->secure()) {
                $datas = $this->art->templaterender(['id', 'title', 'description', 'javascript', 'html', 'header', 'nav', 'aside', 'section', 'footer']);
                echo $this->templates->render('reader', $datas);
            } else {
                echo '<h3>Not enought right to see the article</h3>';
            }

        }


    }

    public function edit()
    {
        echo '<h2>Edit</h2>';
        if($this->importart()) {
            // vue edit art
        }
        
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

    public function update($id, $redir= "home")
    {
        echo '<h2>Update</h2>';        

        $this->art = new Art2($_POST);
        $this->art->updatelinkfrom();
        $this->art->autotaglistcalc($this->artmanager->taglist($this->artmanagergetlister(['id', 'title', 'tag']), $this->art->autotaglist()));
        $this->artmanager->update($this->art);

        

    }
}




?>