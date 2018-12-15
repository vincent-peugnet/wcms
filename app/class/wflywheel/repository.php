<?php
namespace WFlywheel;

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
        return array_map(function($path) use ($ext) {
            return $this->getIdFromPath($path, $ext);
        }, $this->getAllFiles());
    }
}
