<?php

class Controllermedia extends Controller
{
    protected $medialist;
    protected $mediamanager;

    public function __construct() {
        parent::__construct();
        
        $this->mediamanager = new Modelmedia;

    }

    public function desktop()
    {

        if($this->useriseditor()) {



        }
    }

    public function addmedia()
    {
        echo $this->templates->render('media', ['interface' => 'addmedia']);

        //$message = $this->mediamanager->addmedia($_FILES, 2 ** 24, $_POST['id']);

    }


}


?>