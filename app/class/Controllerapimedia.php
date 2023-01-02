<?php

namespace Wcms;

use Error;

class Controllerapimedia extends Controllerapi
{
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
        $basename = $pathinfo['basename'];
        $dirname = $pathinfo['dirname']; //without trailing slash
        try {
            if (Fs::dircheck($dirname)) {
                Fs::writefile(Model::MEDIA_DIR . $path, $file, 0664);
                $this->shortresponse(200, 'File successfully uploaded');
            } else {
                $this->shortresponse(406, 'The target folder directory does not exist');
            }
        } catch (Filesystemexception $e) {
            $this->shortresponse(400, 'Error while saving file: ' . $e->getMessage());
        }
    }
}
