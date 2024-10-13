<?php

namespace Wcms;

use AltoRouter;
use RuntimeException;
use Wcms\Exception\Forbiddenexception;

class Controllermedia extends Controller
{
    protected Modelmedia $mediamanager;

    protected Mediaopt $mediaopt;

    /** @var bool Default action: do not refresh font. */
    protected bool $refreshfont = false;

    public function __construct(AltoRouter $router)
    {
        parent::__construct($router);
        if ($this->user->isvisitor()) {
            http_response_code(401);
            $this->showtemplate('connect', ['route' => 'media']);
            exit;
        }
        $this->mediamanager = new Modelmedia();
        $this->mediaopt = new Mediaopt($_GET);
    }



    public function desktop()
    {
        if (!$this->user->iseditor()) {
            http_response_code(403);
            $this->showtemplate('forbidden');
            exit;
        }
        try {
            Fs::dircheck(Model::FONT_DIR, true, 0775);
            Fs::dircheck(Model::THUMBNAIL_DIR, true, 0775);
            Fs::dircheck(Model::FAVICON_DIR, true, 0775);
            Fs::dircheck(Model::CSS_DIR, true, 0775);
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
            $this->redirect($this->generate("media", [], $this->mediaopt->getpathaddress()));
        }

        $medialist = $this->mediamanager->medialistopt($mediaopt);

        $dirlist = $this->mediamanager->listdir(Model::MEDIA_DIR);

        $pathlist = [];
        $this->mediamanager->listpath($dirlist, '', $pathlist);

        $vars['maxuploadsize'] = readablesize(file_upload_max_size()) . 'o';
        $vars['cssfont'] = Model::dirtopath(Model::FONTS_CSS_FILE);

        if (isset($_GET['display'])) {
            $this->workspace->setmediadisplay($_GET['display']);
            $this->servicesession->setworkspace($this->workspace);
        }

        $vars['filtercode'] = !empty($_POST); // indicate that filter code has been generated
        $vars['medialist'] = $medialist;
        $vars['dirlist'] = $dirlist;
        $vars['pathlist'] = $pathlist;
        $vars['mediaopt'] = $mediaopt;
        $vars['optimizeimage'] = (extension_loaded('imagick') || extension_loaded('gd'));

        $this->showtemplate('media', $vars);
    }

    public function upload()
    {
        if ($this->user->iseditor()) {
            $target = $_POST['dir'] ?? Model::MEDIA_DIR;
            if (!empty($_FILES['file']['name'][0])) {
                $count = count($_FILES['file']['name']);
                try {
                    $this->mediamanager->multiupload(
                        'file',
                        $target,
                        boolval($_POST['idclean']),
                        boolval($_POST['convertimages'])
                    );
                    Model::sendflashmessage("$count file(s) has been uploaded successfully", Model::FLASH_SUCCESS);
                    if ($target === Model::FONT_DIR) {
                        $fontfacer = new Servicefont($this->mediamanager);
                        $fontfacer->writecss();
                    }
                } catch (RuntimeException $e) {
                    Model::sendflashmessage($e->getMessage(), Model::FLASH_ERROR);
                }
            }
            $this->redirect($this->generate('media') . $_POST['route']);
        } else {
            http_response_code(403);
            $this->showtemplate('forbidden');
        }
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
            $this->redirect($this->generate('media') . $_POST['route']);
        } else {
            http_response_code(403);
            $this->showtemplate('forbidden');
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
            parse_str(ltrim($_POST['route'], '?'), $route);
            $route['path'] = $dir . $name;
            $this->routedirect('media', [], $route);
        }
        http_response_code(403);
        $this->showtemplate('forbidden');
    }

    public function folderdelete()
    {
        if ($this->user->issupereditor()) {
            if (isset($_POST['deletefolder']) && intval($_POST['deletefolder']) && isset($_POST['dir'])) {
                try {
                    if ($this->mediamanager->deletedir($_POST['dir'])) {
                        Model::sendflashmessage('Deletion successfull', Model::FLASH_SUCCESS);
                    } else {
                        Model::sendflashmessage('Deletion failed', Model::FLASH_ERROR);
                    }
                } catch (Forbiddenexception $e) {
                    Model::sendflashmessage('Deletion failed: ' . $e->getMessage(), Model::FLASH_ERROR);
                }
            }
            $this->redirect($this->generate('media') . $_POST['route']);
        } else {
            http_response_code(403);
            $this->showtemplate('forbidden');
        }
    }

    public function edit()
    {
        if ($this->user->issupereditor() && isset($_POST['action']) && isset($_POST['id'])) {
            if ($_POST['action'] == 'delete') {
                if ($counter = $this->mediamanager->multifiledelete($_POST['id'])) {
                    Model::sendflashmessage("$counter files deletion successfull", Model::FLASH_SUCCESS);
                } else {
                    Model::sendflashmessage('Error while deleting files', 'error');
                }
            } elseif ($_POST['action'] == 'move' && isset($_POST['dir'])) {
                $this->mediamanager->multimovefile($_POST['id'], $_POST['dir']);
                $this->refreshfont = $_POST['dir'] === Model::FONT_DIR;
            }
            if ($this->refreshfont || $_POST['path'] === Model::FONT_DIR) {
                $fontfacer = new Servicefont($this->mediamanager);
                try {
                    $fontfacer->writecss();
                } catch (Filesystemexception $e) {
                    Model::sendflashmessage('Error while updating fonts CSS : ' . $e->getMessage());
                }
            }
            $this->redirect($this->generate('media') . $_POST['route']);
        } else {
            http_response_code(403);
            $this->showtemplate('forbidden');
        }
    }

    public function rename()
    {
        if (
            $this->user->issupereditor()
            && isset($_POST['oldfilename'])
            && isset($_POST['newfilename'])
            && isset($_POST['dir'])
        ) {
            $oldname = $_POST['dir'] . '/' . $_POST['oldfilename'];
            $newname = $_POST['dir'] . '/' . $_POST['newfilename'];
            try {
                $this->mediamanager->rename($oldname, $newname);
                Model::sendflashmessage("Media file has been successfully renamed", Model::FLASH_SUCCESS);
                if ($_POST['dir'] . '/' === Model::FONT_DIR) {
                    $fontfacer = new Servicefont($this->mediamanager);
                    $fontfacer->writecss();
                }
            } catch (RuntimeException $e) {
                Model::sendflashmessage($e->getMessage(), Model::FLASH_ERROR);
            }
            $this->redirect($this->generate('media') . $_POST['route']);
        } else {
            http_response_code(403);
            $this->showtemplate('forbidden');
        }
    }

    /**
     * Generate the CSS file with the @fontface
     */
    public function fontface()
    {
        if ($this->user->iseditor()) {
            try {
                $fontfacer = new Servicefont($this->mediamanager);
                $fontfacer->writecss();
                Model::sendflashmessage("Font face CSS file  successfully generated", Model::FLASH_SUCCESS);
            } catch (RuntimeException $e) {
                Model::sendflashmessage(
                    "Error while trying to save generated fonts file : " . $e->getMessage(),
                    Model::FLASH_ERROR
                );
            }
            $this->redirect($this->generate("media", [], $this->mediaopt->getpathaddress()));
        } else {
            http_response_code(403);
            $this->showtemplate('forbidden');
        }
    }
}
