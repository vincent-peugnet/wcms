<?php

class Controllermedia extends Controller
{
    /**
     * @var Modelmedia
     */
    protected $mediamanager;

    public function __construct($render)
    {
        parent::__construct($render);

        $this->mediamanager = new Modelmedia;

    }

    public function desktop()
    {
        if ($this->user->iseditor()) {

            if (!$this->mediamanager->basedircheck()) {
                throw new Exception("Media error : Cant create /media folder");
            }
            if (!$this->mediamanager->favicondircheck()) {
                throw new Exception("Media error : Cant create /media/favicon folder");
            }
            if (!$this->mediamanager->thumbnaildircheck()) {
                throw new Exception("Media error : Cant create /media/thumbnail folder");
            }


            $dir = rtrim($_GET['path'] ?? Model::MEDIA_DIR, DIRECTORY_SEPARATOR);

            if(is_dir($dir)) {
                $medialist = $this->mediamanager->getlistermedia($dir . DIRECTORY_SEPARATOR);
                $faviconlist = $this->mediamanager->getlistermedia(Model::FAVICON_DIR);
    
                $dirlist = $this->mediamanager->listdir(Model::MEDIA_DIR);
    
                $this->showtemplate('media', ['medialist' => $medialist, 'faviconlist' => $faviconlist, 'dirlist' => $dirlist, 'dir' => $dir]);
            } else {
                $this->routedirect('media');
            }

        } else {
            $this->routedirect('home');
        }
    }

    public function upload()
    {
        if ($this->user->iseditor()) {
            $target = $_POST['dir'] ?? Model::MEDIA_DIR;
            if (!empty($_FILES['file']['name'][0])) {
                $this->mediamanager->multiupload('file', $target);
            }
                $this->redirect($this->router->generate('media') . '?path=' . $target);
        } else {
            $this->routedirect('home');
        }
    }

    public function folder()
    {
        if ($this->user->iseditor()) {
            $dir = $_POST['dir'] ?? Model::MEDIA_DIR;
            $name = idclean($_POST['foldername']) ?? 'new-folder';
            $this->mediamanager->adddir($dir, $name);
        }
        $this->redirect($this->router->generate('media') . '?path=' . $dir . DIRECTORY_SEPARATOR . $name);

    }


}


?>