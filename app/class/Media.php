<?php

namespace Wcms;

use DateTime;
use DomainException;
use RuntimeException;
use DateTimeInterface;
use Wcms\Exception\Filesystemexception\Fileexception;

class Media extends Item
{
    /** @var string $filename Basename of the file. Ex: `picture.jpeg` */
    protected string $filename;

    /** @var string $dir Directory where the file is stored */
    protected string $dir;

    /** @var string $extension May be the extension of the media file if it have one */
    protected string $extension = "";

    protected string $type;

    protected int $size;

    protected DateTime $date;

    protected ?int $width = null;

    protected ?int $height = null;

    /** @var int Owner of file ID */
    protected ?int $uid = null;

    protected string $permissions;

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
     *
     * @return string[]
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
    public function analyse(): void
    {
        $this->extension = strtolower(pathinfo($this->filename, PATHINFO_EXTENSION));

        $this->settype();

        $this->setdate();

        $path = $this->getlocalpath();

        if ($this->type == $this::IMAGE) {
            list($width, $height, $type, $attr) = getimagesize($path);
            $this->width = empty($width) ? null : $width;
            $this->height = empty($height) ? null : $height;
        }

        $this->permissions = decoct(fileperms($path) & 0777);
        $this->hydrate(stat($path));
    }

    /**
     * Get the Web absolute path. Starting with basepath if W is in a subfolder
     */
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
     * Get the filesystem path to the media file.
     *
     * @return string                       path to a file.
     *                                      This will look like `/home/user/w/media/pictures/hollydays.jpeg`
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
    public function getcode(bool $fullpath = false): string
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

    /**
     * @return int                          Total number of pixels of image
     *
     * @throws DomainException              If not called on an image.
     * @throws RuntimeException             If an error reading width or height of image occured
     */
    public function pixelcount(): int
    {
        if ($this->type !== $this::IMAGE) {
            throw new DomainException('Method getpixelcount() should only be called on images');
        }
        if (is_null($this->width) || $this->width < 1 || is_null($this->height) || $this->height < 1) {
            throw new RuntimeException(
                "impossible to count pixel, error reading width or height of image $this->filename"
            );
        }
        return $this->width * $this->height;
    }

    /**
     * This help to figure out a compression ratio independently from algo
     *
     * @return float                        Bit per pixel ratio of image
     *
     * @throws DomainException              If not called on an image.
     * @throws RuntimeException             If an error reading width or height of image occured
     */
    public function bitperpixel(): float
    {
        $bits = $this->size * 8;
        return $bits / $this->pixelcount();
    }



    // _________________________________________________ G E T ____________________________________________________

    public function filename(): string
    {
        return $this->filename;
    }

    public function dir(): string
    {
        return $this->dir;
    }

    public function extension(): string
    {
        return $this->extension;
    }

    public function type(): string
    {
        return $this->type;
    }

    /**
     * @return int|string
     */
    public function size(string $display = 'binary')
    {
        if ($display == 'hr') {
            return readablesize($this->size) . 'o';
        } else {
            return $this->size;
        }
    }

    /**
     * @return DateTimeInterface|string
     */
    public function date(string $option = 'date')
    {
        return $this->datetransform('date', $option);
    }

    public function width(): ?int
    {
        return $this->width;
    }

    public function height(): ?int
    {
        return $this->height;
    }

    /**
     * @param string $option could be `id` or `name`
     * @return string|int
     */
    public function uid(string $option = 'id')
    {
        if ($option === 'name') {
            $userinfo = posix_getpwuid($this->uid);
            return $userinfo['name'];
        } else {
            return $this->uid;
        }
    }

    public function permissions(): string
    {
        return $this->permissions;
    }

    // ___________________________________________________ S E T __________________________________________________

    public function setfilename(string $filename): void
    {
        $this->filename = $filename;
    }

    public function setdir(string $dir): void
    {
        $this->dir = strip_tags(strtolower($dir));
    }

    /**
     * Automaticaly set the type of the Media using extension
     * If extension is unknown, type will be set to `other`
     */
    public function settype(): void
    {
        if (!empty($this->extension) && isset(self::MEDIA_EXT[$this->extension])) {
            $this->type = self::MEDIA_EXT[$this->extension];
        } else {
            $this->type = 'other';
        }
    }

    public function setsize(int $size): void
    {
        $this->size = $size;
    }

    public function setdate(): void
    {
        $timestamp = filemtime($this->getlocalpath());
        $this->date = new DateTime();
        $this->date->setTimestamp($timestamp);
    }

    public function setwidth(int $width): void
    {
        $this->width = $width;
    }

    public function setheight(int $height): void
    {
        $this->height = $height;
    }

    public function setuid(int $uid): void
    {
        $this->uid = $uid;
    }

    public function setpermissions(string $permissions): void
    {
        $this->permissions = $permissions;
    }
}
