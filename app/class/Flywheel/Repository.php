<?php

namespace Wcms\Flywheel;

use Wcms\Model;

class Repository extends \JamesMoss\Flywheel\Repository
{
    /**
     * Get an array containing the path of all files in this repository
     *
     * @return array An array, item is a file
     */
    public function getAllFiles()
    {
        $ext       = $this->formatter->getFileExtension();
        $files     = glob($this->path . DIRECTORY_SEPARATOR . '*.' . $ext);
        return $files;
    }

    /**
     * Get an array containing the id of all files in this repository
     *
     * @return array An array, item is a id
     */
    public function getAllIds()
    {
        $ext = $this->formatter->getFileExtension();
        return array_map(function ($path) use ($ext) {
            return $this->getIdFromPath($path, $ext);
        }, $this->getAllFiles());
    }

    protected function write($path, $contents): bool
    {
        $ret = parent::write($path, $contents);
        chmod($path, Model::PERMISSION);
        return $ret;
    }
}
