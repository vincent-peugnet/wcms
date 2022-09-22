<?php

namespace Wcms;

/**
 * File system related tools. No Wcms specific param should be set here.
 */
class Fs
{
    public const FILE_PERMISSION    = 0660;
    public const FOLDER_PERMISSION  = 0770;

    /**
     * @param string $filename
     * @param mixed $data
     * @param int $permissions              [optionnal] permission in octal format (a zero before three numbers)
     *
     * @return bool                         False if file is not writen, otherwise true (even if permission warning)
     *
     * @throws Filesystemexception          If an error occured
     *
     * @todo Remove flash messages from here, send FilesystemException instead
     * @todo Verify if the folder exist before writing file. If not, send a specific exception
     * @todo Add a $erase parameter with default true.
     * If set to true, this will protect file overwriting if it already exists.
     * @todo return void instead of bool to force update old verification system
     */
    public static function writefile(string $filename, $data, int $permissions = self::FILE_PERMISSION): bool
    {
        file_put_content_chmod($filename, $data, $permissions);

        return true;
    }

    /**
     * Copy folder folder files and subbfolders recursively
     *
     * @param string $src source folder
     * @param string $dst destination folder
     * @param int $perm OPTIONNAL permission in octal format.
     */
    public static function recursecopy($src, $dst, $perm = Model::FOLDER_PERMISSION): void
    {
        $dir = opendir($src);
        mkdir($dst, $perm);
        while (false !== ( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . '/' . $file)) {
                    self::recursecopy($src . '/' . $file, $dst . '/' . $file, $perm);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }


    /**
     * Delete a file from the filesystem
     *
     * @param string $file                      Filename to delete
     *
     * @throws Notfoundexception                If file does not exist
     * @throws Unlinkexception                  If PHP unlink function fails
     */
    public static function delete(string $file): void
    {
        if (!file_exists($file)) {
            throw new Notfoundexception($file);
        }
        if (!unlink($file)) {
            throw new Unlinkexception($file);
        }
    }
}
