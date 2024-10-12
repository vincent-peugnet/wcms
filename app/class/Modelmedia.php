<?php

namespace Wcms;

use DomainException;
use ErrorException;
use InvalidArgumentException;
use RuntimeException;
use Imagick;
use ImagickException;
use Wcms\Exception\Forbiddenexception;

class Modelmedia extends Model
{
    public const MEDIA_SORTBY = [
        'filename' => 'filename',
        'size' => 'size',
        'type' => 'type',
        'date' => 'date',
        'extension' => 'extension'
    ];

    public const ID_REGEX                   = "%[^a-z0-9-_.]%";

    public const CONVERSION_MAX_WIDTH       = 1920;
    public const CONVERSION_MAX_HEIGHT      = 1920;
    public const CONVERSION_QUALITY         = 50;

    /**
     * @return Media[]                      sorted array of Media
     */
    public function medialistopt(Mediaopt $mediaopt): array
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
     * @return Media[] of Media objects
     */
    public function getlistermedia($dir, $type = []): array
    {
        if (is_dir($dir) && $handle = opendir($dir)) {
            $list = [];
            while (false !== ($entry = readdir($handle))) {
                if (!empty($entry) && $entry != "." && $entry != "..") {
                    try {
                        $media = new Media($dir . $entry);
                        if (empty($type) || in_array($media->type(), $type)) {
                            $list[] = $media;
                        }
                    } catch (RuntimeException $e) {
                        Logger::errorex($e);
                    }
                }
            }
                return $list;
        }
        return [];
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

    public function mediacompare($media1, $media2, $method = 'filename', $order = 1)
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

    /**
     * @return string[]                     listing css theme files
     */
    public function listthemes(): array
    {
        return $this->globlist(self::THEME_DIR, ['css']);
    }

    /**
     * @return string[]                     List of paths
     */
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
     * @throws RuntimeException             If CURL execution failed
     * @throws Filesystemexception          If writing file failed
     *
     * @todo clean ID of a file
     * @todo switch to fopen method if CURL is not installed
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
     * @param string $index                 Id of the file input
     * @param string $target                direction to save the files
     * @param bool $idclean                 clean the filename using idclean
     * @param bool $convertimages           resize and compress images if any
     *
     * @throws Folderexception if target folder does not exist
     * @throws RuntimeException if upload fail.
     */
    public function multiupload(string $index, string $target, bool $idclean = false, bool $convertimages = false): void
    {
        $target = trim($target, "/") . "/";
        $this->checkdir($target);
        $count = 0;
        $successcount = 0;
        $failedconversion = 0;
        foreach ($_FILES[$index]['name'] as $filename) {
            $fileinfo = pathinfo($filename);
            if ($idclean) {
                $extension = self::idclean($fileinfo['extension']);
                $id = self::idclean($fileinfo['filename']);
            } else {
                $extension = $fileinfo['extension'];
                $id = $fileinfo['filename'];
            }

            $from = $_FILES[$index]['tmp_name'][$count];
            $count++;
            $to = mktmpdir('w-media-upload') . '/' . $id . '.' . $extension;
            if (move_uploaded_file($from, $to)) {
                try {
                    $media = new Media($to);
                    if ($convertimages && $media->type() === Media::IMAGE) {
                        $media = $this->convertimage($media);
                    }
                    if (rename($media->getabsolutepath(), $target . $media->filename())) {
                        $successcount++;
                    }
                } catch (Fileexception $e) {
                    // transfert failed
                } catch (RuntimeException | ImagickException $e) {
                    $failedconversion++;
                }
            }
        }

        if ($successcount < $count || $failedconversion > 0) {
            $message = "$successcount / $count files have been uploaded";
            $message .= $failedconversion > 0 ? " and $failedconversion image(s) conversion(s) failed" : '';
            throw new RuntimeException($message);
        }
    }

    /**
     * @param string $dir                   directory path
     *
     * @throws Folderexception if target folder does not exist or is outside `/media` folder
     */
    public function checkdir(string $dir)
    {
        if (!is_dir($dir)) {
            throw new Folderexception("directory `$dir` does not exists");
        }
        if (strpos($dir, "media/") !== 0) {
            throw new Folderexception("directory `$dir`, is not inside `/media` folder");
        }
    }

    /**
     * @todo replace with Fs class
     */
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
     *
     * @throws  Forbiddenexception If the directory is not inside `/media` folder
     *
     * @todo return void and throw exception in case of failure
     */
    public function deletedir(string $dir): bool
    {
        if (strpos($dir, "media/") !== 0) {
            throw new Forbiddenexception("directory `$dir`, is not inside `/media` folder");
        }
        if (mb_substr($dir, -1) !== '/') {
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
     * @throws Filesystemexception          If an error occured
     */
    public function delete(Media $media): void
    {
        Fs::deletefile($media->getlocalpath());
    }

    /**
     * @param string[] $files               List of file paths to delete
     * @return int                          number of successfull deletion
     */
    public function multifiledelete(array $files): int
    {
        $counter = 0;
        foreach ($files as $filedir) {
            if (is_string($filedir)) {
                try {
                    Fs::deletefile($filedir);
                    $counter++;
                } catch (Filesystemexception $e) {
                }
            } else {
                throw new InvalidArgumentException('$files argument should be an array containing strings');
            }
        }
        return $counter;
    }

    /**
     * @param string $filedir current file path
     * @param string $dir New directory to move file to
     *
     * @return bool True in case of success, false if the file does not exist or if `rename()` fail
     */
    public function movefile(string $filedir, string $dir): bool
    {
        if (mb_substr($dir, -1) !== '/') {
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
     *
     * @throws Filesystemexception          if cant access file
     */
    public function rename(string $oldname, string $newname)
    {
        $newbasename = trim(basename($newname));
        $newdirname = dirname($newname);
        if (empty($newbasename)) {
            throw new Fileexception("new name of file cannot be empty");
        }

        $newname = "$newdirname/$newbasename";

        Fs::accessfile($oldname);
        Fs::accessfile($newname);

        if (!file_exists($oldname)) {
            throw new Fileexception("File : $oldname does not exist");
        }
        return rename($oldname, $newname);
    }

    /**
     * Convert an image to Webp format using Max width and height.
     *
     * @param Media $media                  Media to convert. It have to be an image.
     *                                      Otherwise will thow a DomainException
     * @param bool $deleteoriginal          Choose if original media file should be deleted. Default is true.
     *
     * @return Media                        Converted Media object
     *
     * @throws RuntimeException             If imagick is not installed
     * @throws ImagickException             If an error occured during IM process
     * @throws Filesystemexception          If deleting the original media failed, or if file creation failed.
     */
    private function convertimage(Media $media, $deleteoriginal = true): Media
    {
        if ($media->type() !== Media::IMAGE) {
            throw new DomainException('Given Media should be an image');
        }
        if (!extension_loaded('imagick')) {
            throw new RuntimeException('Imagick PHP extension is not installed');
        }
        $image = new Imagick($media->getlocalpath());
        $image->adaptiveResizeImage(
            min($image->getImageWidth(), $this::CONVERSION_MAX_WIDTH),
            min($image->getImageHeight(), $this::CONVERSION_MAX_HEIGHT),
            true
        );
        $image->setImageFormat('webp');
        $image->setImageCompressionQuality($this::CONVERSION_QUALITY);

        $convertmediapath = $media->dir() . '/' . $media->getbasefilename() . '.webp';
        if ($image->writeImage($convertmediapath) && $deleteoriginal) {
            Fs::deletefile($media->getlocalpath());
        }
        return new Media($convertmediapath);
    }


    /**
     * Generate a clickable folder tree based on reccurive array
     */
    public static function treecount(
        array $dirlist,
        string $dirname,
        int $deepness,
        string $path,
        string $currentdir,
        Mediaopt $mediaopt
    ) {
        $selected = $path . '/' === $currentdir;
        if ($selected) {
            $folder = '├─<i class="fa fa-folder-open-o"></i> <span>' . $dirname . '<span>';
        } else {
            $folder = '├─<i class="fa fa-folder-o"></i> ' . $dirname;
        }
        $class = $selected ? ' class="selected"' : '';
        echo "<tr$class>";
        $href = $mediaopt->getpathaddress($path);
        $foldername = str_repeat('&nbsp;&nbsp;', $deepness) . $folder;
        echo "<td><a href=\"$href\">$foldername</a></td>";
        echo '<td>' . $dirlist['dirfilecount'] . '</td>';
        echo '</tr>';
        foreach ($dirlist as $key => $value) {
            if (is_array($value)) {
                self::treecount(
                    $value,
                    $key,
                    $deepness + 1,
                    $path . DIRECTORY_SEPARATOR . $key,
                    $currentdir,
                    $mediaopt
                );
            }
        }
    }
}
