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
            $dir = $_GET['path'] ?? Model::MEDIA_DIR;

            $medialist = $this->mediamanager->getlistermedia($dir . DIRECTORY_SEPARATOR);
            $faviconlist = $this->mediamanager->getlistermedia(Model::FAVICON_DIR);

            $dirlist = $this->mediamanager->listdir(Model::MEDIA_DIR);

            $this->showtemplate('media', ['medialist' => $medialist, 'faviconlist' => $faviconlist, 'dirlist' => $dirlist, 'dir' => $dir]);
        }
    }

    public function addmedia()
    {
        echo $this->templates->render('media', ['interface' => 'addmedia']);

        //$message = $this->mediamanager->addmedia($_FILES, 2 ** 24, $_POST['id']);

    }

    public function upload()
    {
        $target = $_POST['dir'] ?? Model::MEDIA_DIR;
        if($target[strlen($target)-1]!='/')
                $target=$target.'/';
            $count=0;
            foreach ($_FILES['file']['name'] as $filename) 
            {
                $fileinfo = pathinfo($filename);
                $extension = idclean($fileinfo['extension']);
                $id = idclean($fileinfo['filename']);

                $temp=$target;
                $tmp=$_FILES['file']['tmp_name'][$count];
                $count=$count + 1;
                $temp .=  $id .'.' .$extension;
                move_uploaded_file($tmp,$temp);
                $temp='';
                $tmp='';
            }
        $this->redirect($this->router->generate('media').'?path='.$target);
    }


}


?>