<?php

namespace Wcms;

use AltoRouter;
use Error;
use RuntimeException;

class Controllerapimedia extends Controllerapi
{
    protected Modelmedia $mediamanager;

    public function __construct(AltoRouter $router)
    {
        parent::__construct($router);
        $this->mediamanager = new Modelmedia();
    }

    /**
     * Upload a single file to a target directory. Folders are created automatically.
     * It will erase any already existing file.
     */
    public function upload(string $path): void
    {
        if (!$this->user->iseditor()) {
            $this->shortresponse(403, 'Unauthorized to upload files');
        }
        try {
            $file = $this->getrequestbody();
        } catch (Error $e) {
            $this->shortresponse(400, 'Error while reading the stream: ' . $e->getMessage());
        }
        $path = rawurldecode($path);
        $pathinfo = pathinfo($path);
        $dirname = Model::MEDIA_DIR . $pathinfo['dirname']; //without trailing slash
        try {
            Fs::dircheck($dirname, true, 0775);
            Fs::writefile(Model::MEDIA_DIR . $path, $file, 0664);
            $this->shortresponse(200, 'File successfully uploaded');
        } catch (Filesystemexception $e) {
            $this->shortresponse(400, 'Error while saving file: ' . $e->getMessage());
        }
    }

    public function delete(string $path): void
    {
        if (!$this->user->iseditor()) {
            $this->shortresponse(403, 'Unauthorized to upload files');
        }
        try {
            $media = new Media(Model::MEDIA_DIR . $path);
            $this->mediamanager->delete($media);
        } catch (RuntimeException $e) {
            $this->shortresponse(400, 'Error while deleting media file: ' . $e->getMessage());
        }
    }
}
