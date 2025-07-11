<?php

namespace Wcms;

use DomainException;
use InvalidArgumentException;
use RuntimeException;
use Imagick;
use ImagickException;
use Wcms\Exception\Filesystemexception;
use Wcms\Exception\Filesystemexception\Fileexception;
use Wcms\Exception\Filesystemexception\Folderexception;
use Wcms\Exception\Forbiddenexception;
use Wcms\Exception\Missingextensionexception;

class Modelmedia extends Model
{
    public const MEDIA_SORTBY = [
        'filename' => 'filename',
        'size' => 'size',
        'type' => 'type',
        'date' => 'date',
        'extension' => 'extension'
    ];

    /** Characters that are authorized for cleaned media filename */
    public const ID_AUTHORIZED_CHARS                   = 'a-z0-9-_.';

    public const OPTIMIZE_IMG_MAX_WIDTH     = 1920;
    public const OPTIMIZE_IMG_MAX_HEIGHT    = 1920;
    public const OPTIMIZE_IMG_QUALITY       = 60;
    public const OPTIMIZE_IMG_MAX_BPP       = 0.5;
    public const OPTIMIZE_IMG_ALLOWED_EXT   = [
        'jpg' => 'imagecreatefromjpeg',
        'jpeg' => 'imagecreatefromjpeg',
        'png' => 'imagecreatefrompng',
        'webp' => 'imagecreatefromwebp',
        'bmp' => 'imagecreatefrombmp',
    ];

    /**
     * @return Media[]                      sorted array of Media
     *
     * @throws Folderexception              If dir is not a valid folder
     */
    public function medialistopt(Mediaopt $mediaopt): array
    {
        $medialist = $this->getlistermedia($mediaopt);
        $this->medialistsort($medialist, $mediaopt->sortby(), $mediaopt->order());

        return $medialist;
    }

    /**
     * get a list of media of selected types
     *
     * @param Mediaopt $mediaopt            Media option filter Object
     *
     * @return Media[]                      array of Media objects
     *
     * @throws Folderexception              When the given folder isn't a directory
     */
    protected function getlistermedia(Mediaopt $mediaopt): array
    {
        $dir = $mediaopt->dir();
        $types = $mediaopt->type();
        if (!is_dir($dir)) {
            throw new Folderexception("$dir is not a directory");
        }
        $list = [];
        $files = scandir($dir);
        foreach ($files as $file) {
            if (is_file($dir . $file)) {
                try {
                    $media = new Media($dir . $file);
                    if (empty($types) || in_array($media->type(), $types)) {
                        $list[] = $media;
                    }
                } catch (RuntimeException $e) {
                    Logger::errorex($e);
                }
            }
        }
        return $list;
    }


    /**
     * Sort an array of media
     *
     * @param Media[] $medialist
     * @param string $sortby
     * @param int $order                    Can be 1 or -1
     */
    protected function medialistsort(array &$medialist, string $sortby = 'id', int $order = 1): bool
    {
        $sortby = (in_array($sortby, self::MEDIA_SORTBY)) ? $sortby : 'id';
        $order = ($order === 1 || $order === -1) ? $order : 1;
        return usort($medialist, $this->buildsorter($sortby, $order));
    }

    protected function buildsorter(string $sortby, int $order): callable
    {
        return function ($media1, $media2) use ($sortby, $order) {
            $result = $this->mediacompare($media1, $media2, $sortby, $order);
            return $result;
        };
    }

    protected function mediacompare(Media $media1, Media $media2, string $method = 'filename', int $order = 1): int
    {
        $result = ($media1->$method() <=> $media2->$method());
        return $result * $order;
    }




    /**
     * @return string[]
     */
    public function listfavicon(): array
    {
        $faviconlist = $this->globlist(self::FAVICON_DIR, ['ico', 'png', 'jpg', 'jpeg', 'gif']);
        return $faviconlist;
    }

    /**
     * @return string[]
     */
    public function listthumbnail(): array
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
     * @param string[] $extensions
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
     * Create a tree structure of folders that lead to the given path.
     *
     * @param string $path
     *
     * @param array<string, mixed> $dirlist
     *
     * @return Folder                       a Folder object containing any number of childs
     *
     * @throws RuntimeException if path does not exist
     */
    public function foldercrumb(string $path, array $dirlist): Folder
    {
        $path = rtrim($path, '/');
        $levels = explode('/', $path);
        $currentpath = '';
        $deepness = 0;
        foreach ($levels as $level) {
            $currentpath .= $level;
            if (!key_exists($level, $dirlist)) {
                throw new RuntimeException('path do not match given tree of directories');
            }
            if (isset($f)) {
                if (!key_exists($level, $f->childs)) {
                    throw new RuntimeException('invalid path');
                }
                $f = $f->childs[$level];
            } else {
                $p = new Folder($level, [], filecount($currentpath), $currentpath, $deepness);
                $f = $p;
            }
            $f->open = true;
            $deepness++;
            $dirlist = $dirlist[$level];
            foreach ($dirlist as $folder => $content) {
                $folderpath = "$currentpath/$folder";
                $f->childs[$folder] = new Folder($folder, [], filecount($folderpath), $folderpath, $deepness);
            }
            $currentpath .= '/';
        }
        $f->selected = true;
        if (!isset($p)) {
            throw new RuntimeException('invalid path');
        }
        return $p;
    }

    /**
     * Convert tree of Folders into list of Folders
     *
     * @param Folder $folder                A tree stored as Folder
     *
     * @return Folder[]                     List of folders
     */
    public function crumb(Folder $folder): array
    {
        $list[] = $folder;
        foreach ($folder->childs as $folder) {
            $list = array_merge($list, $this->crumb($folder));
        }
        return $list;
    }



    /**
     * Generate an recursive array where each folder is a array
     *
     * @return array<string, mixed>
     *
     * @throws RuntimeException             If dir is not a valid directory
     */
    public function listdir(string $dir): array
    {
        $result = [];

        $cdir = subfolders($dir);
        foreach ($cdir as $value) {
            $result[$value] = $this->listdir("$dir/$value");
        }

        return $result;
    }

    /**
     * Analyse recursive array of content to generate list of path
     *
     * @param array<string, mixed> $dirlist Array generated by the listdir function
     * @param string $parent used to create the strings
     * @param string[] $pathlist used by reference, must be an empty array
     *
     * @return string[]
     */
    public function listpath(array $dirlist, string $parent = '', array &$pathlist = []): array
    {
        foreach ($dirlist as $dir => $content) {
            if (is_array($content)) {
                $pathlist[] = "$parent$dir/";
                $this->listpath($content, "$parent$dir/", $pathlist);
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
            $url = strip_tags($url);
            throw new RuntimeException("Invalid URL: $url");
        }
        $firstheader = get_headers($url)[0];
        if (!strstr($firstheader, "200 OK")) {
            throw new RuntimeException("server response is $firstheader");
        }
        $file = curl_download($url);
        Fs::writefile($target . basename($url), $file, 0664);
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
        $tmpdir = mktmpdir('w-media-upload');
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
            $to = "$tmpdir/$id.$extension";
            if (move_uploaded_file($from, $to)) {
                try {
                    $media = new Media($to);
                    if ($convertimages && $media->type() === Media::IMAGE) {
                        $media = $this->optimizeimage($media);
                    }
                    if (rename($media->getlocalpath(), $target . $media->filename())) {
                        $successcount++;
                    } else {
                        Logger::error('failed to move file from tmp dir to media folder');
                    }
                } catch (Fileexception $e) {
                    Logger::errorex($e);
                } catch (RuntimeException | ImagickException $e) {
                    Logger::errorex($e);
                    $failedconversion++;
                }
            }
        }
        rmdir($tmpdir);

        if ($successcount < $count || $failedconversion > 0) {
            $message = "$successcount / $count files have been uploaded";
            $message .= $failedconversion > 0 ? " and $failedconversion image(s) conversion(s) failed" : '';
            throw new RuntimeException($message);
        }
    }

    /**
     * @param string $dir                   directory path
     *
     * @return void
     *
     * @throws Folderexception if target folder does not exist
     */
    public function checkdir(string $dir): void
    {
        if (!is_dir($dir)) {
            throw new Folderexception("directory `$dir` does not exists");
        }
    }

    /**
     * @todo replace with Fs class
     */
    public function adddir(string $dir, string $name): bool
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
     * @todo replace by Fs class
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
     * Function to recursively delete a directory
     *
     * @todo replace with Fs class
     */
    public function deltree(string $dir): bool
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
                    Logger::errorex($e);
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
     * @param string[] $filedirlist list of file list
     * @param string $dir New directory to move file to
     *
     * @return int Number of moved files
     */
    public function multimovefile(array $filedirlist, string $dir): int
    {
        $count = 0;
        foreach ($filedirlist as $filedir) {
            if (is_string($filedir)) {
                if ($this->movefile($filedir, $dir)) {
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * @param string $oldname
     * @param string $newname
     *
     * @throws Filesystemexception          if cant access file
     *
     * @todo Use a Media as input
     */
    public function rename(string $oldname, string $newname): bool
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
     * Optimize an image to Webp format and limit width and height.
     * But only if image is not already compressed and normal sized.
     *
     * @param Media $media                  Media to convert. It have to be an image.
     *                                      Otherwise will thow a DomainException
     * @param bool $deleteoriginal          Choose if original media file should be deleted. Default is true.
     *
     * @return Media                        Converted Media object
     *
     * @throws Missingextensionexception    If nor imagick or is installed
     * @throws ImagickException             If an error occured during IM process
     * @throws Filesystemexception          If deleting the original media failed, or if file creation failed.
     */
    private function optimizeimage(Media $media, bool $deleteoriginal = true): Media
    {
        if ($media->type() !== Media::IMAGE) {
            throw new DomainException('Given Media should be an image');
        }

        try {
            if (
                !key_exists($media->extension(), $this::OPTIMIZE_IMG_ALLOWED_EXT) ||
                (
                    $media->bitperpixel() < $this::OPTIMIZE_IMG_MAX_BPP &&
                    $media->width() <= $this::OPTIMIZE_IMG_MAX_WIDTH &&
                    $media->height() <= $this::OPTIMIZE_IMG_MAX_HEIGHT
                )
            ) {
                return $media; // image is already well compressed
            }
        } catch (RuntimeException $e) {
            Logger::errorex($e);
            return $media; // PHP could not get image dimensions. It may be beccause of supporting new formats.
        }

        $convertmediapath = $media->dir() . '/' . $media->getbasefilename() . '.webp';

        if (extension_loaded('imagick')) {
            $image = new Imagick($media->getlocalpath());
            $image->adaptiveResizeImage(
                min($image->getImageWidth(), $this::OPTIMIZE_IMG_MAX_WIDTH),
                min($image->getImageHeight(), $this::OPTIMIZE_IMG_MAX_HEIGHT),
                true
            );
            $image->setImageFormat('webp');
            $image->setImageCompressionQuality($this::OPTIMIZE_IMG_QUALITY);

            $convertmediapath = $media->dir() . '/' . $media->getbasefilename() . '.webp';
            $conversionsuccess = $image->writeImage($convertmediapath);
        } elseif (extension_loaded('gd')) {
            $gdfunction = $this::OPTIMIZE_IMG_ALLOWED_EXT[$media->extension()];
            $image = $gdfunction($media->getlocalpath());

            $heightdiff = $media->height() - $this::OPTIMIZE_IMG_MAX_HEIGHT;
            $widthdiff = $media->width() - $this::OPTIMIZE_IMG_MAX_WIDTH;

            if ($heightdiff > 0 || $widthdiff > 0) {
                if ($heightdiff > $widthdiff) {
                    $height = $this::OPTIMIZE_IMG_MAX_HEIGHT;
                    $width = intval($media->width() * ($this::OPTIMIZE_IMG_MAX_HEIGHT / $media->height()));
                } else {
                    $height = intval($media->height() * ($this::OPTIMIZE_IMG_MAX_WIDTH / $media->width()));
                    $width = $this::OPTIMIZE_IMG_MAX_WIDTH;
                }

                $image = imagescale($image, $width, $height);
            }
            $conversionsuccess = imagewebp($image, $convertmediapath, $this::OPTIMIZE_IMG_QUALITY);
        } else {
            throw new Missingextensionexception('Nor imagick or gd PHP extension is installed');
        }

        if ($conversionsuccess && $deleteoriginal && $convertmediapath !== $media->getlocalpath()) {
            Fs::deletefile($media->getlocalpath());
        }
        return new Media($convertmediapath);
    }
}
