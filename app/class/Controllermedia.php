<?php

namespace Wcms;

use Exception;

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

            if (!$this->mediamanager->dircheck(Model::MEDIA_DIR)) {
                throw new Exception("Media error : Cant create /media folder");
            }
            if (!$this->mediamanager->dircheck(Model::FAVICON_DIR)) {
                throw new Exception("Media error : Cant create /media/favicon folder");
            }
            if (!$this->mediamanager->dircheck(Model::THUMBNAIL_DIR)) {
                throw new Exception("Media error : Cant create /media/thumbnail folder");
            }
            
            $mediaopt = new Medialist($_GET);
            if(empty($mediaopt->path())) {
                $mediaopt->setpath(DIRECTORY_SEPARATOR . Model::MEDIA_DIR);
            }

            if(is_dir($mediaopt->dir())) {
                $medialist = $this->mediamanager->medialistopt($mediaopt);
    
                $dirlist = $this->mediamanager->listdir(Model::MEDIA_DIR);

                $pathlist = [];
                $this->mediamanager->listpath($dirlist, '', $pathlist);

    
                $this->showtemplate('media', ['medialist' => $medialist, 'dirlist' => $dirlist, 'pathlist' =>$pathlist, 'mediaopt' => $mediaopt]);
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
                $this->redirect($this->router->generate('media') . '?path=/' . $target);
        } else {
            $this->routedirect('home');
        }
    }

    public function folderadd()
    {
        if ($this->user->iseditor()) {
            $dir = $_POST['dir'] ?? Model::MEDIA_DIR;
            $name = idclean($_POST['foldername']) ?? 'new-folder';
            $this->mediamanager->adddir($dir, $name);
        }
        $this->redirect($this->router->generate('media') . '?path=/' . $dir . DIRECTORY_SEPARATOR . $name);

    }

    public function folderdelete()
    {
        if(isset($_POST['dir'])) {
            if(isset($_POST['deletefolder']) && intval($_POST['deletefolder']) && $this->user->issupereditor()) {
                $this->mediamanager->deletedir($_POST['dir']);
            } else {
                $this->redirect($this->router->generate('media') . '?path=/' . $_POST['dir']);
                exit;
            }
        }
        $this->redirect($this->router->generate('media'));
    }

    public function edit()
    {
        if($this->user->issupereditor() && isset($_POST['action']) && isset($_POST['id'])) {
            if($_POST['action'] == 'delete') {
                $this->mediamanager->multifiledelete($_POST['id']);
            } elseif ($_POST['action'] == 'move' && isset($_POST['dir'])) {
                $this->mediamanager->multimovefile($_POST['id'], $_POST['dir']);
            }
        }
        $this->redirect($this->router->generate('media') . '?path=/' . $_POST['path']);
    }


}


?>