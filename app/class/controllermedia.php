<?php

class Controllermedia extends Controller
{
    protected $medialist;
    protected $mediamanager;

    public function __construct($render) {
        parent::__construct($render);
        
        $this->mediamanager = new Modelmedia;

    }

    public function desktop()
    {
        if($this->user->iseditor()) {
            $medialist = $this->mediamanager->getlistermedia(Model::MEDIA_DIR);
            $faviconlist = $this->mediamanager->getlistermedia(Model::FAVICON_DIR);
            $this->showtemplate('media', ['medialist' => $medialist, 'faviconlist' => $faviconlist]);
        }
    }

    public function addmedia()
    {
        echo $this->templates->render('media', ['interface' => 'addmedia']);

        //$message = $this->mediamanager->addmedia($_FILES, 2 ** 24, $_POST['id']);

    }


}


?>