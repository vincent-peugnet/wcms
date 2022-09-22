<?php

namespace Wcms;

class Modeladmin extends Model
{
    /**
     * List all availalble pages databases
     *
     * @return array
     */
    public function pagesdblist(): array
    {
        $dblist = glob(self::PAGES_DIR . '*', GLOB_ONLYDIR);
        $dblist = array_map('basename', $dblist);

        return $dblist;
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
