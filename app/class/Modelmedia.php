<?php

namespace Wcms;

use ErrorException;
use Exception;
use InvalidArgumentException;
use phpDocumentor\Reflection\Types\Mixed_;
use RuntimeException;

class Modelmedia extends Model
{
    public const MEDIA_EXT = [
        'jpg' => 'image',
        'jpeg' => 'image',
        'png' => 'image',
        'gif' => 'image',
        'ico' => 'image',
        'tiff' => 'image',
        'bmp' => 'image',
        'svg' => 'image',
        'mp3' => 'sound',
        'opus' => 'sound',
        'wav' => 'sound',
        'ogg' => 'sound',
        'flac' => 'sound',
        'aiff' => 'sound',
        'm4a' => 'sound',
        'mp4' => 'video',
        'mkv' => 'video',
        'avi' => 'video',
        'mov' => 'video',
        'wmv' => 'video',
        'm4v' => 'video',
        'webm' => 'video',
        'zip' => 'archive',
        '7zip' => 'archive',
        'pdf' => 'document',
        'odt' => 'document',
        'doc' => 'document',
        'docx' => 'document',
        'woff' => 'font',
        'woff2' => 'font',
        'otf' => 'font',
        'ttf' => 'font',
        'js' => 'code',
        'html' => 'code',
        'css' => 'code',
        'php' => 'code',
        '' => 'other'
    ];

    public const MEDIA_SORTBY = [
        'id' => 'id',
        'size' => 'size',
        'type' => 'type',
        'date' => 'date',
        'extension' => 'extension'
    ];

    /**
     * Retrun a list of Media types
     */
    public static function mediatypes(): array
    {
        return array_unique(array_values(self::MEDIA_EXT));
    }

    /**
     * Get the Media Object
     *
     * @param string $entry Id of the file
     * @param string $dir Directory of media file
     *
     * @return Media|bool
     */
    public function getmedia(string $entry, string $dir)
    {
        $fileinfo = pathinfo($entry);

        if (isset($fileinfo['extension'])) {
            $datas = array(
                'id' => str_replace('.' . $fileinfo['extension'], '', $fileinfo['filename']),
                'path' => $dir,
                'extension' => $fileinfo['extension']
            );
            return new Media($datas);
        } else {
            return false;
        }
    }

    public function medialistopt(Mediaopt $mediaopt)
    {
        $medialist = $this->getlistermedia($mediaopt->dir(), $mediaopt->type());
        $this->medialistsort($medialist, $mediaopt->sortby(), $mediaopt->order());

        return $medialist;
    }

    /**
     * get a list of media of selected types
     *
     * @param string $dir Media directory ot look at
     * @param array $type
     *
     * @return Media[]|bool of Media objects
     */
    public function getlistermedia($dir, $type = [])
    {
        if (is_dir($dir) && $handle = opendir($dir)) {
            $list = [];
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $media = $this->getmedia($entry, $dir);

                    if ($media != false) {
                        $media->analyse();

                        if (empty($type) || in_array($media->type(), $type)) {
                            $list[] = $media;
                        }
                    }
                }
            }
                return $list;
        }
        return false;
    }


    /**
     * Sort an array of media
     *
     * @param array $medialist
     * @param string $sortby
     * @param int $order Can be 1 or -1
     */
    public function medialistsort(array &$medialist, string $sortby = 'id', int $order = 1): bool
    {
        $sortby = (in_array($sortby, self::MEDIA_SORTBY)) ? $sortby : 'id';
        $order = ($order === 1 || $order === -1) ? $order : 1;
        return usort($medialist, $this->buildsorter($sortby, $order));
    }

    public function buildsorter($sortby, $order)
    {
        return function ($media1, $media2) use ($sortby, $order) {
            $result = $this->mediacompare($media1, $media2, $sortby, $order);
            return $result;
        };
    }

    public function mediacompare($media1, $media2, $method = 'id', $order = 1)
    {
        $result = ($media1->$method() <=> $media2->$method());
        return $result * $order;
    }





    public function listfavicon()
    {
        $faviconlist = $this->globlist(self::FAVICON_DIR, ['ico', 'png', 'jpg', 'jpeg', 'gif']);
        return $faviconlist;
    }

    public function listthumbnail()
    {
        $faviconlist = $this->globlist(self::THUMBNAIL_DIR, ['ico', 'png', 'jpg', 'jpeg', 'gif']);
        return $faviconlist;
    }


    public function listinterfacecss()
    {
        $listinterfacecss = $this->globlist(self::ASSETS_CSS_DIR, ['css']);
        $listinterfacecss = array_diff($listinterfacecss, ['edit.css', 'home.css', 'tagcolors.css']);
        return $listinterfacecss;
    }

    public function globlist(string $dir = '', array $extensions = []): array
    {
        $list = [];
        if (empty($extensions)) {
            $glob = $dir . '*.';
        } else {
            foreach ($extensions as $extension) {
                $glob = $dir . '*.' . $extension;
                $list = array_merge($list, glob($glob));
            }
        }
        $list = array_map(function ($input) {
            return basename($input);
        }, $list);
        return $list;
    }


    /**
     * Generate an recursive array where each folder is a array and containing a filecount in each folder
     */
    public function listdir(string $dir): array
    {
        $result = array();

        $cdir = scandir($dir);
        $result['dirfilecount'] = 0;
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".", ".."))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = $this->listdir($dir . DIRECTORY_SEPARATOR . $value);
                } else {
                    $result['dirfilecount']++;
                }
            }
        }

        return $result;
    }

    /**
     * Analyse recursive array of content to generate list of path
     *
     * @param array $dirlist Array generated by the listdir function
     * @param string $parent used to create the strings
     * @param array $pathlist used by reference, must be an empty array
     *
     * @return array list of path as string
     */
    public function listpath(array $dirlist, string $parent = '', array &$pathlist = [])
    {
        foreach ($dirlist as $dir => $content) {
            if (is_array($content)) {
                $pathlist[] = $parent . $dir . DIRECTORY_SEPARATOR;
                $this->listpath($content, $parent . $dir . DIRECTORY_SEPARATOR, $pathlist);
            }
        }
        return $pathlist;
    }

    /**
     * Upload single file
     *
     * @param string $index The file id
     * @param string $destination File final destination
     * @param bool|int $maxsize Max file size in octets
     * @param bool|array $extensions List of authorized extensions
     * @param bool $jpgrename Change the file exentension to .jpg
     *
     * @return bool If upload process is a succes or not
     */
    public function simpleupload(
        string $index,
        string $destination,
        $maxsize = false,
        $extensions = false,
        bool $jpgrename = false
    ): bool {
        //Test1: if the file is corectly uploaded
        if (!isset($_FILES[$index]) || $_FILES[$index]['error'] > 0) {
            return false;
        }
        //Test2: check file size
        if ($maxsize !== false && $_FILES[$index]['size'] > $maxsize) {
            return false;
        }
        //Test3: check extension
        $ext = substr(strrchr($_FILES[$index]['name'], '.'), 1);
        if ($extensions !== false && !in_array($ext, $extensions)) {
            return false;
        }
        if ($jpgrename !== false) {
            $destination .= '.jpg';
        } else {
            $destination .= '.' . $ext;
        }
        //Move to dir
        return move_uploaded_file($_FILES[$index]['tmp_name'], $destination);
    }

    /**
     * @throws RuntimeException             If CURL execution failed
     * @throws Filesystemexception          If writing file failed
     *
     * @todo clean ID of a file
     * @todo switch to fopen mothod if CURL is not installed
     */
    public function urlupload(string $url, string $target): void
    {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            Model::sendflashmessage('invalid url: ' . strip_tags($url), 'error');
        }
        if (!strstr(get_headers($url)[0], "200 OK")) {
            Model::sendflashmessage(get_headers($url)[0], 'error');
        }

        try {
            $file = curl_download($url);
            Fs::writefile($target . basename($url), $file, 0664);
        } catch (ErrorException $e) {
            Model::sendflashmessage('file not uploaded beccause : ' . $e->getMessage(), Model::FLASH_ERROR);
            // switch to fopen mothod if CURL is not installed
            // $file = fopen($data, 'r');
            // if ($file !== false) {
            //     if ($target[strlen($target) - 1] != DIRECTORY_SEPARATOR) {
            //         $target .= DIRECTORY_SEPARATOR;
            //     }
            // }
        }
    }

    /**
     * Upload multiple files
     *
     * @param string $index Id of the file input
     * @param string $target direction to save the files
     */
    public function multiupload(string $index, string $target)
    {
        if ($target[strlen($target) - 1] != DIRECTORY_SEPARATOR) {
            $target .= DIRECTORY_SEPARATOR;
        }
        $count = 0;
        $successcount = 0;
        foreach ($_FILES[$index]['name'] as $filename) {
            $fileinfo = pathinfo($filename);
            $extension = self::idclean($fileinfo['extension']);
            $id = self::idclean($fileinfo['filename']);

            $tmp = $_FILES['file']['tmp_name'][$count];
            $count++;
            $temp = $target . $id . '.' . $extension;
            if (move_uploaded_file($tmp, $temp)) {
                $successcount++;
            }
            $temp = '';
            $tmp = '';
        }
        Model::sendflashmessage($successcount . ' / ' . $count . ' files have been uploaded', 'success');
    }

    public function adddir($dir, $name)
    {
        $newdir = $dir . DIRECTORY_SEPARATOR . $name;
        if (!is_dir($newdir)) {
            return mkdir($newdir);
        } else {
            return false;
        }
    }

    /**
     * Completely delete dir and it's content
     *
     * @param string $dir Directory to destroy
     *
     * @return bool depending on operation success
     */
    public function deletedir(string $dir): bool
    {
        if (substr($dir, -1) !== '/') {
            $dir .= '/';
        }
        if (is_dir($dir)) {
            return $this->deltree($dir);
        } else {
            return false;
        }
    }

    /**
     * Function do recursively delete a directory
     */
    public function deltree(string $dir)
    {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->deltree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    /**
     * Delete a file
     */
    public function deletefile(string $filedir)
    {
        if (is_file($filedir) && is_writable(dirname($filedir))) {
            return unlink($filedir);
        } else {
            return false;
        }
    }

    public function multifiledelete(array $filelist)
    {
        $success = [];
        foreach ($filelist as $filedir) {
            if (is_string($filedir)) {
                $success[] = $this->deletefile($filedir);
            }
        }
        if (in_array(false, $success)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param string $filedir current file path
     * @param string $dir New directory to move file to
     *
     * @return bool True in case of success, false if the file does not exist or if `rename()` fail
     */
    public function movefile(string $filedir, string $dir): bool
    {
        if (substr($dir, -1) !== '/') {
            $dir .= '/';
        }
        if (is_file($filedir)) {
            $newdir = $dir . basename($filedir);
            return rename($filedir, $newdir);
        } else {
            return false;
        }
    }

    /**
     * @param array $filedirlist Ordered array of file list
     * @param string $dir New directory to move file to
     *
     * @return bool False if any of moves failed, otherwise true
     */
    public function multimovefile(array $filedirlist, string $dir): bool
    {
        $count = 0;
        foreach ($filedirlist as $filedir) {
            if (is_string($filedir)) {
                if ($this->movefile($filedir, $dir)) {
                    $count++;
                }
            }
        }
        $total = count($filedirlist);
        if ($count !== $total) {
            Model::sendflashmessage($count . ' / ' . $total . ' files have been moved', 'error');
            return false;
        } else {
            Model::sendflashmessage($count . ' / ' . $total . ' files have been moved', 'success');
            return true;
        }
    }

    /**
     * @param string $oldname
     * @param string $newname
     * @throws InvalidArgumentException if cant access file
     */
    public function rename(string $oldname, string $newname)
    {
        try {
            accessfile($oldname);
            accessfile($newname);
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
        if (!file_exists($oldname)) {
            throw new InvalidArgumentException("File : $oldname does not exist");
        }
        return rename($oldname, $newname);
    }
}
