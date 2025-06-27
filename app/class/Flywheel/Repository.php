<?php

namespace Wcms\Flywheel;

use JamesMoss\Flywheel\Config;
use RuntimeException;
use Wcms\Model;

class Repository extends \JamesMoss\Flywheel\Repository
{
    /**
     * @throws RuntimeException when error in writing repo folder
     */
    public function __construct($name, Config $config)
    {
        parent::__construct($name, $config);
        if (!chmod($this->path, Model::FOLDER_PERMISSION)) {
            throw new RuntimeException("error while trying to change permission of database folder: " . $this->path);
        }
    }

    /**
     * Get an array containing the path of all files in this repository
     *
     * @return string[]|false An array, item is a file
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
     * @return string[] An array, item is a id
     */
    public function getAllIds(): array
    {
        $ext = $this->formatter->getFileExtension();
        return array_map(function ($path) use ($ext) {
            return $this->getIdFromPath($path, $ext);
        }, $this->getAllFiles());
    }

    /**
     * @param string $path
     * @param string $contents
     */
    protected function write($path, $contents): bool
    {
        $ret = parent::write($path, $contents);
        chmod($path, Model::FILE_PERMISSION);
        return $ret;
    }
}
