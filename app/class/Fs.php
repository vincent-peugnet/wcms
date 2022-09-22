<?php

namespace Wcms;

use DomainException;

/**
 * File system related tools. No Wcms specific param should be set here.
 */
abstract class Fs
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
        self::fileputcontentchmod($filename, $data, $permissions);

        return true;
    }



    /**
     * @param string $filename                  Path to the file where to write the data.
     * @param mixed $data                       The data to write.
     * @param int $permissions                  in octal value
     *
     * @return bool                             Indicate if the file is new
     *
     * @throws Filesystemexception              when file_put_contents fails
     * @throws DomainException                  If permissions are not valid
     * @throws Chmodexception                   when chmod fails
     */
    protected static function fileputcontentchmod(string $filename, $data, int $permissions): bool
    {
        if ($permissions < 0600 || $permissions > 0777) {
            throw new DomainException("$permissions is an incorrect permissions value");
        }
        if (!is_dir(dirname($filename))) {
            throw new Folderexception("Folder does not exist");
        }
        $new = !file_exists($filename);
        $length = file_put_contents($filename, $data);
        if ($length === false) {
            throw new Filesystemexception("Error while writing $filename");
        }
        if (!chmod($filename, $permissions)) {
            throw new Chmodexception("Error while setting file permissions $filename", $permissions);
        }
        return $new;
    }



    /**
     * Check if dir exist. If not, create it.
     *
     * @param string $dir                       Directory to check
     * @return bool                             return true if the dir already exist or was created succesfullt.
     *                                          Otherwise return false
     *
     * @throws Folderexception If folder creation is impossible or if directory does not exist
     */
    public static function dircheck(string $dir): bool
    {
        if (!is_dir($dir)) {
            $parent = dirname($dir);
            if (self::dircheck($parent)) {
                if (mkdir($dir)) {
                    return true;
                } else {
                    throw new Folderexception("Cannot create directory : $dir");
                }
            } else {
                return false;
            }
        } else {
            return true;
        }
    }


    /**
     * Check if a file is accessible or can be writen
     *
     * @param string $path                      file path to check
     * @param bool $createdir                   create directory if does not exist
     * @return bool                             If no error occured
     *
     * @throws Folderexception if parent directory does not exist | is not writable
     * @throws Fileexception if file exist and is not writable
     *
     * @todo This function should send Runtime errors insted of LogicError
     */
    public static function accessfile(string $path, bool $createdir = false): bool
    {
        $dir = dirname($path);
        if (self::dircheck($dir)) {
            if (!is_writable($dir)) {
                throw new Folderexception("Directory '$dir' is not writable.");
            }
            if (is_file($path) && !is_writable($path)) {
                throw new Fileexception("The file '$path' is not writable.");
            }
            return true;
        } else {
            return false;
        }
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
