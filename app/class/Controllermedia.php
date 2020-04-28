<?php

namespace Wcms;

use \Exception;
use \LogicException;

class Controllermedia extends Controller
{
    /**
     * @var Modelmedia
     */
    protected $mediamanager;

    public function __construct($render)
    {
        parent::__construct($render);

        $this->mediamanager = new Modelmedia();
    }

    /**
     * @throws Exception
     */
    public function desktop()
    {
        if ($this->user->iseditor()) {
            try {
                dircheck(Model::FONT_DIR);
                dircheck(Model::THUMBNAIL_DIR);
                dircheck(Model::FAVICON_DIR);
                dircheck(Model::CSS_DIR);
            } catch (\InvalidArgumentException $exception) {
                throw new LogicException($exception->getMessage());
            }
            
            $mediaopt = new Medialist($_GET);
            if (empty($mediaopt->path())) {
                $mediaopt->setpath(DIRECTORY_SEPARATOR . Model::MEDIA_DIR);
            }

            if (is_dir($mediaopt->dir())) {
                $medialist = $this->mediamanager->medialistopt($mediaopt);
    
                $dirlist = $this->mediamanager->listdir(Model::MEDIA_DIR);

                $pathlist = [];
                $this->mediamanager->listpath($dirlist, '', $pathlist);

                if (isset($_GET['display'])) {
                    $this->session->addtosession('mediadisplay', $_GET['display']);
                }

                $vars['display'] = $this->session->mediadisplay;
                $vars['medialist'] = $medialist;
                $vars['dirlist'] = $dirlist;
                $vars['pathlist'] = $pathlist;
                $vars['mediaopt'] = $mediaopt;
    
                $this->showtemplate('media', $vars);
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
                $this->redirect($this->generate('media') . '?path=/' . $target);
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
            $this->redirect($this->generate('media') . '?path=/' . $dir . DIRECTORY_SEPARATOR . $name);
        }
        $this->routedirect('home');
    }

    public function folderdelete()
    {
        if (isset($_POST['dir'])) {
            if (isset($_POST['deletefolder']) && intval($_POST['deletefolder']) && $this->user->issupereditor()) {
                $this->mediamanager->deletedir($_POST['dir']);
            } else {
                $this->redirect($this->generate('media') . '?path=/' . $_POST['dir']);
                exit;
            }
        }
        $this->redirect($this->generate('media'));
    }

    public function edit()
    {
        if ($this->user->issupereditor() && isset($_POST['action']) && isset($_POST['id'])) {
            if ($_POST['action'] == 'delete') {
                if ($this->mediamanager->multifiledelete($_POST['id'])) {
                    Model::sendflashmessage('Files deletion successfull', 'success');
                } else {
                    Model::sendflashmessage('Error while deleting files', 'error');
                }
            } elseif ($_POST['action'] == 'move' && isset($_POST['dir'])) {
                $this->mediamanager->multimovefile($_POST['id'], $_POST['dir']);
            }
        }
        $this->redirect($this->generate('media') . '?path=/' . $_POST['path']);
    }
}
