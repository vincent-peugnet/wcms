<?php

namespace Wcms;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Wcms\Exception\Forbiddenexception;

class Media extends Item
{
    /** @var string $filename Basename of the file. Ex: `picture.jpeg` */
    protected string $filename;

    /** @var string $dir Directory where the file is stored */
    protected string $dir;

    /** @var string $extension May be the extension of the media file if it have one */
    protected string $extension = "";

    protected string $type;

    protected $size;

    protected $date;

    protected $width;

    protected $height;

    protected $length;

    /** @var int Owner of file ID */
    protected ?int $uid = null;

    protected $permissions;

    public const IMAGE      = "image";
    public const SOUND      = "sound";
    public const VIDEO      = "video";
    public const ARCHIVE    = "archive";
    public const DOCUMENT   = "document";
    public const FONT       = "font";
    public const CODE       = "code";
    public const OTHER      = "other";

    public const MEDIA_EXT = [
        'jpg'   => self::IMAGE,
        'jpeg'  => self::IMAGE,
        'png'   => self::IMAGE,
        'gif'   => self::IMAGE,
        'ico'   => self::IMAGE,
        'tiff'  => self::IMAGE,
        'bmp'   => self::IMAGE,
        'svg'   => self::IMAGE,
        'webp'  => self::IMAGE,
        'avif'  => self::IMAGE,
        'mp3'   => self::SOUND,
        'opus'  => self::SOUND,
        'wav'   => self::SOUND,
        'ogg'   => self::SOUND,
        'flac'  => self::SOUND,
        'aiff'  => self::SOUND,
        'm4a'   => self::SOUND,
        'mp4'   => self::VIDEO,
        'mkv'   => self::VIDEO,
        'avi'   => self::VIDEO,
        'mov'   => self::VIDEO,
        'wmv'   => self::VIDEO,
        'm4v'   => self::VIDEO,
        'webm'  => self::VIDEO,
        'zip'   => self::ARCHIVE,
        '7zip'  => self::ARCHIVE,
        'tar'   => self::ARCHIVE,
        'rar'   => self::ARCHIVE,
        'pdf'   => self::DOCUMENT,
        'odt'   => self::DOCUMENT,
        'doc'   => self::DOCUMENT,
        'docx'  => self::DOCUMENT,
        'woff'  => self::FONT,
        'woff2' => self::FONT,
        'otf'   => self::FONT,
        'ttf'   => self::FONT,
        'js'    => self::CODE,
        'html'  => self::CODE,
        'css'   => self::CODE,
        'php'   => self::CODE,
        ''      => self::OTHER,
    ];

    /**
     * Retrun a list of Media types
     */
    public static function mediatypes(): array
    {
        return array_unique(array_values(self::MEDIA_EXT));
    }

    // _____________________________________________________ F U N ____________________________________________________

    /**
     * @throws Fileexception                If path is not a file
     */
    public function __construct(string $path)
    {
        if (!is_file($path)) {
            throw new Fileexception("$path is not a file");
        }
        $this->filename = basename($path);
        $this->dir = pathinfo($path, PATHINFO_DIRNAME);
        $this->analyse();
    }

    /**
     * This will analyse the media file. Set the extension, date, perms
     */
    public function analyse()
    {
        $this->extension = strtolower(pathinfo($this->filename, PATHINFO_EXTENSION));

        $this->settype();

        $this->setdate();

        $path = $this->getlocalpath();

        if ($this->type == 'image') {
            list($width, $height, $type, $attr) = getimagesize($path);
            $this->width = $width;
            $this->height = $height;
        }

        $this->permissions = decoct(fileperms($path) & 0777);
        $this->hydrate(stat($path));
    }


    public function getabsolutepath(): string
    {
        if (!empty(Config::basepath())) {
            $base = '/' . Config::basepath();
        } else {
            $base = '';
        }
        $fullpath = $base . '/' . $this->dir() . '/' . $this->filename();
        $fullpath = str_replace('\\', '/', $fullpath);
        return $fullpath;
    }

    /**
     * Relative path to media starting with `./media`
     */
    public function getincludepath(): string
    {
        $includepath = './' . $this->dir . '/' . $this->filename();
        $includepath = str_replace('\\', '/', $includepath);
        return $includepath;
    }

    /**
     * Get the relative filesystem path to the media file.
     *
     * @return string                       Relative path to a file.
     *                                      This will look like `media/pictures/hollydays.jpeg`
     */
    public function getlocalpath(): string
    {
        return $this->dir . '/' . $this->filename;
    }

    /**
     * Generate html code depending on media type
     * @param bool $fullpath option to use fullpath of file instead of W rendered one. default is false
     * @return string html code
     */
    public function getcode($fullpath = false): string
    {
        if ($fullpath) {
            $src = $this->getabsolutepath();
        } else {
            $src = $this->getincludepath();
        }

        switch ($this->type) {
            case self::IMAGE:
                $code = '![' . $this->filename . '](' . $src . ')';
                break;

            case self::SOUND:
                $code = "<audio controls src=\"$src\"></audio>";
                break;

            case self::VIDEO:
                $code = "<video controls src=\"$src\"></video>";
                break;

            case self::FONT:
                if ("$this->dir/" === Model::FONT_DIR) {
                    $font = new Font([$this]);
                    $code = $font->getcode();
                    break;
                } // intentional fall-through

            default:
                $code = '[' . $this->filename . '](' . $src . ')';
                break;
        }

        return $code;
    }

    public function getsymbol(): string
    {
        switch ($this->type) {
            case self::IMAGE:
                $symbol = 'file-picture-o';
                break;

            case self::SOUND:
                $symbol = "file-sound-o";
                break;

            case self::VIDEO:
                $symbol = "file-movie-o";
                break;

            case self::DOCUMENT:
                $symbol = "file-pdf-o";
                break;

            case self::ARCHIVE:
                $symbol = "file-archive-o";
                break;

            case self::CODE:
                $symbol = "file-code-o";
                break;

            case self::FONT:
                $symbol = "font";
                break;

            default:
                $symbol = "file-o";
                break;
        }
        return $symbol;
    }

    /**
     * @return string                       Filename __without extension__
     */
    public function getbasefilename(): string
    {
        $pathinfo = pathinfo($this->filename);
        return $pathinfo['filename'];
    }



    // _________________________________________________ G E T ____________________________________________________

    public function filename()
    {
        return $this->filename;
    }

    public function dir()
    {
        return $this->dir;
    }

    public function extension()
    {
        return $this->extension;
    }

    public function type()
    {
        return $this->type;
    }

    public function size($display = 'binary')
    {
        if ($display == 'hr') {
            return readablesize($this->size) . 'o';
        } else {
            return $this->size;
        }
    }

    public function date($option = 'date')
    {
        return $this->datetransform('date', $option);
    }

    public function width()
    {
        return $this->width;
    }

    public function height()
    {
        return $this->height;
    }

    public function length()
    {
        return $this->length;
    }

    /**
     * @param string $option could be `id` or `name`
     * @return string|int
     */
    public function uid($option = 'id')
    {
        if ($option === 'name') {
            $userinfo = posix_getpwuid($this->uid);
            return $userinfo['name'];
        } else {
            return $this->uid;
        }
    }

    public function permissions()
    {
        return $this->permissions;
    }

    // ___________________________________________________ S E T __________________________________________________

    public function setfilename($filename)
    {
        if (is_string($filename)) {
            $this->filename = $filename;
        }
    }

    public function setdir($dir)
    {
        if (is_string($dir)) {
            $this->dir = strip_tags(strtolower($dir));
        }
    }

    /**
     * Automaticaly set the type of the Media using extension
     * If extension is unknown, type will be set to `other`
     */
    public function settype()
    {
        if (!empty($this->extension) && isset(self::MEDIA_EXT[$this->extension])) {
            $this->type = self::MEDIA_EXT[$this->extension];
        } else {
            $this->type = 'other';
        }
    }

    public function setsize($size)
    {
        if (is_int($size)) {
            $this->size = $size;
        }
    }

    public function setdate()
    {
        $timestamp = filemtime($this->getlocalpath());
        $this->date = new DateTime();
        $this->date->setTimestamp($timestamp);
    }

    public function setwidth($width)
    {
        if (is_int($width)) {
            $this->width = $width;
        }
    }

    public function setheight($height)
    {
        if (is_int($height)) {
            $this->height = $height;
        }
    }

    public function setlength($length)
    {
        if ($this->type == 'sound') {
            $this->length = $length;
        }
    }

    public function setuid($uid)
    {
        if (is_int($uid)) {
            $this->uid = $uid;
        }
    }

    public function setpermissions($permissions)
    {
        $this->permissions = $permissions;
    }
}
