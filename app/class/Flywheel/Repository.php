<?php

namespace Wcms\Flywheel;

use JamesMoss\Flywheel\Config;
use RuntimeException;
use Wcms\Model;

class Repository extends \JamesMoss\Flywheel\Repository
{
    /**
     * Constructor
     *
     * @param string $name   The name of the repository. Must match /[A-Za-z0-9_-]{1,63}+/
     * @param Config $config The config to use for this repo
     */
    public function __construct($name, Config $config)
    {
        // Setup class properties
        $this->name          = $name;
        $this->path          = $config->getPath() . DIRECTORY_SEPARATOR . $name;
        $this->formatter     = $config->getOption('formatter');
        $this->queryClass    = $config->getOption('query_class');
        $this->documentClass = $config->getOption('document_class');

        // Ensure the repo name is valid
        $this->validateName($this->name);

        // Ensure directory exists and we can write there
        if (!is_dir($this->path)) {
            if (!@mkdir($this->path, Model::FOLDER_PERMISSION, true)) {
                throw new \RuntimeException(sprintf('`%s` doesn\'t exist and can\'t be created.', $this->path));
            }
        } else if (!is_writable($this->path)) {
            throw new \RuntimeException(sprintf('`%s` is not writable.', $this->path));
        }
    }


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
        chmod($path, Model::FILE_PERMISSION);
        return $ret;
    }
}
