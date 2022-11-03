<?php

namespace Wcms;

use RuntimeException;

class Controllermedia extends Controller
{
    protected Modelmedia $mediamanager;

    protected Mediaopt $mediaopt;

    public function __construct($render)
    {
        parent::__construct($render);
        $this->mediamanager = new Modelmedia();

        $this->mediaopt = new Mediaopt($_GET);
    }



    public function desktop()
    {
        if ($this->user->iseditor()) {
            try {
                Fs::dircheck(Model::FONT_DIR, true);
                Fs::dircheck(Model::THUMBNAIL_DIR, true);
                Fs::dircheck(Model::FAVICON_DIR, true);
                Fs::dircheck(Model::CSS_DIR, true);
            } catch (RuntimeException $e) {
                Model::sendflashmessage($e->getMessage(), Model::FLASH_ERROR);
            }
            if (isset($_POST['query'])) {
                $datas = array_merge($_GET, $_POST);
            } else {
                $datas = $_GET;
            }

            $mediaopt = new Mediaoptlist($datas);

            try {
                $this->mediamanager->checkdir($this->mediaopt->dir());
            } catch (Folderexception $e) {
                Model::sendflashmessage($e->getMessage(), Model::FLASH_WARNING);
                $this->mediaopt->setpath(Model::MEDIA_DIR);
                $this->redirect($this->generate("media", [], $this->mediaopt->getpathadress()));
            }

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
            $this->routedirect('home');
        }
    }

    public function upload()
    {
        if ($this->user->iseditor()) {
            $target = $_POST['dir'] ?? Model::MEDIA_DIR;
            if (!empty($_FILES['file']['name'][0])) {
                $count = count($_FILES['file']['name']);
                try {
                    $this->mediamanager->multiupload('file', $target, boolval($_POST['idclean']));
                    Model::sendflashmessage("$count file(s) has been uploaded successfully", Model::FLASH_SUCCESS);
                    $this->redirect($this->generate('media') . '?path=/' . $target);
                } catch (RuntimeException $e) {
                    Model::sendflashmessage($e->getMessage(), Model::FLASH_ERROR);
                }
            }
        } else {
            Model::sendflashmessage("acces denied", Model::FLASH_ERROR);
        }
        $this->routedirect('media');
    }

    public function urlupload()
    {
        if ($this->user->iseditor()) {
            $target = $_POST['dir'] ?? Model::MEDIA_DIR;
            if (!empty($_POST['url'])) {
                try {
                    $this->mediamanager->urlupload($_POST['url'], $target);
                } catch (RuntimeException $e) {
                    Model::sendflashmessage('Error while uploading : ' . $e->getMessage(), Model::FLASH_ERROR);
                }
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
                } catch (RuntimeException $e) {
                    Model::sendflashmessage($e->getMessage(), 'error');
                }
            } else {
                Model::sendflashmessage('Invalid name or extension', 'warning');
            }
        }
        $this->redirect($this->generate('media') . $_POST['route']);
    }

    public function fontface()
    {
        if ($this->user->iseditor()) {
            $medias = $this->mediamanager->getlistermedia(Model::FONT_DIR, [Media::FONT]);
            $fontfacer = new Servicefont($medias);
            $fontcss = $fontfacer->css();
            try {
                Fs::writefile(Model::FONTS_CSS_FILE, $fontcss, 0664);
                Model::sendflashmessage("Font face CSS file  successfully generated", Model::FLASH_SUCCESS);
            } catch (RuntimeException $e) {
                Model::sendflashmessage(
                    "Error while trying to save generated fonts file : " . $e->getMessage(),
                    Model::FLASH_ERROR
                );
            }
        } else {
            Model::sendflashmessage("Access denied", Model::FLASH_ERROR);
        }
        $this->redirect($this->generate("media", [], $this->mediaopt->getpathadress()));
    }
}
