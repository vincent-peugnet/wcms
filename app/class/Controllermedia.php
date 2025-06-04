<?php

namespace Wcms;

use AltoRouter;
use RuntimeException;
use Wcms\Exception\Filesystemexception\Folderexception;
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
            $this->sendflashmessage($e->getMessage(), self::FLASH_ERROR);
        }
        if (isset($_POST['query'])) {
            $datas = array_merge($_GET, $_POST);
        } else {
            $datas = $_GET;
        }

        $mediaopt = new Mediaoptlist($datas);

        try {
            Fs::dircheck($this->mediaopt->dir());
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
            $vars['foldercrumb'] = $this->mediamanager->crumb(
                $this->mediamanager->foldercrumb(
                    $this->mediaopt->dir(),
                    [basename(Model::MEDIA_DIR) => $dirlist] // add media folder as parent node
                )
            );

            $this->showtemplate('media', $vars);
        } catch (RuntimeException $e) {
            // TODO: instead of redirecting show an error template
            $this->sendflashmessage($e->getMessage(), self::FLASH_WARNING);
            $this->mediaopt->setpath(Model::MEDIA_DIR);
            $this->redirect($this->generate('media', [], $this->mediaopt->getpathaddress()));
        }
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
                    $this->sendflashmessage("$count file(s) has been uploaded successfully", self::FLASH_SUCCESS);
                    if ($target === Model::FONT_DIR) {
                        $fontfacer = new Servicefont($this->mediamanager);
                        $fontfacer->writecss();
                    }
                } catch (RuntimeException $e) {
                    $this->sendflashmessage($e->getMessage(), self::FLASH_ERROR);
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
                    $this->sendflashmessage('Error while uploading : ' . $e->getMessage(), self::FLASH_ERROR);
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
                        $this->sendflashmessage('Deletion successfull', self::FLASH_SUCCESS);
                    } else {
                        $this->sendflashmessage('Deletion failed', self::FLASH_ERROR);
                    }
                } catch (Forbiddenexception $e) {
                    $this->sendflashmessage('Deletion failed: ' . $e->getMessage(), self::FLASH_ERROR);
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
        if ($this->user->issupereditor() && isset($_POST['action'])) {
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                $this->sendflashmessage('no media selected', self::FLASH_ERROR);
                $this->redirect($this->generate('media') . $_POST['route']);
            }
            if ($_POST['action'] === 'delete') {
                if ($counter = $this->mediamanager->multifiledelete($_POST['id'])) {
                    $this->sendflashmessage("$counter files deletion successfull", self::FLASH_SUCCESS);
                } else {
                    $this->sendflashmessage('Error while deleting files', self::FLASH_ERROR);
                }
            }
            if ($_POST['action'] === 'move') {
                if (!isset($_POST['dir']) || empty($_POST['dir'])) {
                    $this->sendflashmessage('no direction selected', self::FLASH_ERROR);
                    $this->redirect($this->generate('media') . $_POST['route']);
                }
                $count = $this->mediamanager->multimovefile($_POST['id'], $_POST['dir']);

                $total = count($_POST['id']);
                if ($count !== $total) {
                    $this->sendflashmessage($count . ' / ' . $total . ' files have been moved', self::FLASH_ERROR);
                } else {
                    $this->sendflashmessage($count . ' / ' . $total . ' files have been moved', self::FLASH_SUCCESS);
                }
                $this->refreshfont = $_POST['dir'] === Model::FONT_DIR;
            }
            if ($this->refreshfont || $_POST['path'] === Model::FONT_DIR) {
                try {
                    $fontfacer = new Servicefont($this->mediamanager);
                    $fontfacer->writecss();
                } catch (RuntimeException $e) {
                    $this->sendflashmessage('Error while updating fonts CSS : ' . $e->getMessage());
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
                $this->sendflashmessage("Media file has been successfully renamed", self::FLASH_SUCCESS);
                if ($_POST['dir'] . '/' === Model::FONT_DIR) {
                    $fontfacer = new Servicefont($this->mediamanager);
                    $fontfacer->writecss();
                }
            } catch (RuntimeException $e) {
                $this->sendflashmessage($e->getMessage(), self::FLASH_ERROR);
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
                $this->sendflashmessage("Font face CSS file  successfully generated", self::FLASH_SUCCESS);
            } catch (RuntimeException $e) {
                $this->sendflashmessage(
                    "Error while trying to save generated fonts file : " . $e->getMessage(),
                    self::FLASH_ERROR
                );
            }
            $this->redirect($this->generate("media", [], $this->mediaopt->getpathaddress()));
        } else {
            http_response_code(403);
            $this->showtemplate('forbidden');
        }
    }
}
