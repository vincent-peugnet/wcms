<?php

class Controllermedia extends Controller
{
    protected $mediamanager;

    public function __construct($render) {
        parent::__construct($render);
        
        $this->mediamanager = new Modelmedia;

    }

    public function desktop()
    {
        if($this->user->iseditor()) {
            $dir = rtrim($_GET['path'] ?? Model::MEDIA_DIR, DIRECTORY_SEPARATOR);

            $medialist = $this->mediamanager->getlistermedia($dir . DIRECTORY_SEPARATOR);
            $faviconlist = $this->mediamanager->getlistermedia(Model::FAVICON_DIR);

            $dirlist = $this->mediamanager->listdir(Model::MEDIA_DIR);

            $this->showtemplate('media', ['medialist' => $medialist, 'faviconlist' => $faviconlist, 'dirlist' => $dirlist, 'dir' => $dir]);
        } else {
            $this->routedirect('home');
        }
    }

    public function upload()
    {
        if($this->user->iseditor()) {
        $target = $_POST['dir'] ?? Model::MEDIA_DIR;

        $this->mediamanager->upload($target);
        $this->redirect($this->router->generate('media').'?path='.$target);
        } else {
            $this->routedirect('home');
        }
    }


}


?>