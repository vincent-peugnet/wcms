<?php

namespace Wcms;

use Exception;
use InvalidArgumentException;
use LogicException;

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
            if (isset($_POST['query']) && $this->user->iseditor()) {
                $datas = array_merge($_GET, $_POST);
            } else {
                $datas = $_GET;
            }

            $mediaopt = new Mediaopt($datas);
            if (empty($mediaopt->path())) {
                $mediaopt->setpath(DIRECTORY_SEPARATOR . Model::MEDIA_DIR);
            }

            if (is_dir($mediaopt->dir())) {
                $medialist = $this->mediamanager->medialistopt($mediaopt);

                $dirlist = $this->mediamanager->listdir(Model::MEDIA_DIR);

                $pathlist = [];
                $this->mediamanager->listpath($dirlist, '', $pathlist);

                $vars['maxuploadsize'] = readablesize(file_upload_max_size()) . 'o';

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

    public function urlupload()
    {
        if ($this->user->iseditor()) {
            $target = $_POST['dir'] ?? Model::MEDIA_DIR;
            if (!empty($_POST['url'])) {
                $this->mediamanager->urlupload($_POST['url'], $target);
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
            $name = Model::idclean($_POST['foldername']);
            if ($name == "") {
                $name = 'new-folder';
            }
            $this->mediamanager->adddir($dir, $name);
            $this->redirect($this->generate('media') . '?path=/' . $dir . $name);
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
        $this->redirect($this->generate('media') . $_POST['route']);
    }

    public function rename()
    {
        if (
            $this->user->issupereditor()
            && isset($_POST['oldid'])
            && isset($_POST['newid'])
            && isset($_POST['oldextension'])
            && isset($_POST['newextension'])
            && isset($_POST['path'])
        ) {
            $newid = Model::idclean($_POST['newid']);
            $newextension = Model::idclean($_POST['newextension']);
            if (!empty($newid) && !empty($newextension)) {
                $oldname = $_POST['path'] . $_POST['oldid'] . '.' . $_POST['oldextension'];
                $newname = $_POST['path'] . $newid . '.' . $newextension;
                try {
                    $this->mediamanager->rename($oldname, $newname);
                } catch (InvalidArgumentException $e) {
                    Model::sendflashmessage($e->getMessage(), 'error');
                }
            } else {
                Model::sendflashmessage('Invalid name or extension', 'warning');
            }
        }
        $this->redirect($this->generate('media') . $_POST['route']);
    }
}
