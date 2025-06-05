<?php

namespace Wcms;

use RuntimeException;

class Modeladmin extends Model
{
    /**
     * List all availalble pages tables
     *
     * @return Folder[]                     Array of Folder objects.
     *
     * @throws RuntimeException            If pages folder is broken
     */
    public function pagetables(): array
    {
        try {
            $dbs = subfolders(Model::PAGES_DIR);
            $folders = [];
            foreach ($dbs as $db) {
                $path = Model::PAGES_DIR . $db;
                $filecount = filecount($path);
                $folder = new Folder($db, [], $filecount, $path, 1);
                if ($db === Config::pagetable()) {
                    $folder->selected = true;
                }
                $folders[] = $folder;
            }
            return $folders;
        } catch (RuntimeException $e) {
            $m = $e->getMessage();
            throw new RuntimeException("Error when trying to list page tables: $m");
        }
    }

    /**
     * Duplicate current page database using new name
     *
     * @param string $name of the new database
     */
    public function duplicate(string $name): void
    {
        $this->copydb(Config::pagetable(), $name);
    }

    /**
     * Copy database folder to a new folder if it doeas not already exsit
     *
     * @param string $db name of source page database to copy
     * @param string $name of the destination database
     */
    public function copydb(string $db, string $name): void
    {
        $dbdir = self::PAGES_DIR . $db;
        $newdbdir = self::PAGES_DIR . Model::idclean($name);
        if (is_dir($dbdir) && !is_dir($newdbdir)) {
            Fs::recursecopy($dbdir, $newdbdir);
        }
    }
}
