<?php

namespace Wcms;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Exception;

class Media extends Item
{
    protected $id;
    protected $path;
    protected $extension;
    protected $type;
    protected $size;
    protected $date;
    protected $width;
    protected $height;
    protected $length;
    protected $uid;
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

    public function __construct(array $donnees)
    {
        $this->hydrate($donnees);
    }

    public function analyse()
    {
        $this->settype();

        $this->setdate();

        $filepath = $this->path . $this->id . '.' . $this->extension;

        if ($this->type == 'image') {
            list($width, $height, $type, $attr) = getimagesize($filepath);
            $this->width = $width;
            $this->height = $height;
        }

        $stat = stat($filepath);

        $permissions = decoct(fileperms($filepath) & 0777);

        $this->setpermissions($permissions);

        $this->hydrate($stat);
    }


    public function getfullpath()
    {
        if (!empty(Config::basepath())) {
            $base = '/' . Config::basepath();
        } else {
            $base = '';
        }
        $fullpath = $base . '/' . $this->path() . $this->id() . '.' . $this->extension();
        $fullpath = str_replace('\\', '/', $fullpath);
        return $fullpath;
    }

    public function getincludepath()
    {
        $includepath = $this->path() . $this->id() . '.' . $this->extension();
        $includepath = str_replace('\\', '/', $includepath);
        $includepath = substr($includepath, 6);
        return $includepath;
    }

    public function getfulldir()
    {
        return $this->path . $this->id . '.' . $this->extension;
    }

    /**
     * Generate html code depending on media type
     * @param bool $fullpath option to use fullpath of file instead of W rendered one. default is false
     * @return string html code
     */
    public function getcode($fullpath = false): string
    {
        if ($fullpath === true) {
            $src = $this->getfullpath();
        } else {
            $src = $this->getincludepath();
        }

        switch ($this->type) {
            case 'image':
                $code = '![' . $this->id . '](' . $src . ')';
                break;

            case 'sound':
                    $code = '<audio controls src="' . $src . '"></audio>';
                break;

            case 'video':
                $ext = $this->extension;
                $code = '<video controls=""><source src="' . $src . '" type="video/' . $ext . '"></video>';
                break;

            default:
                    $code = '[' . $this->id . '](' . $src . ')';
                break;
        }

        return $code;
    }

    public function getsymbol()
    {
        switch ($this->type) {
            case 'image':
                $symbol = 'picture-o';
                break;

            case 'sound':
                $symbol = "sound-o";
                break;

            case 'video':
                $symbol = "movie-o";
                break;

            case 'document':
                $symbol = "pdf-o";
                break;

            case 'archive':
                $symbol = "archive-o";
                break;

            case 'code':
                $symbol = "code-o";
                break;

            default:
                $symbol = "o";
                break;
        }
        return $symbol;
    }



// _________________________________________________ G E T ____________________________________________________

    public function id()
    {
        return $this->id;
    }

    public function path()
    {
        return $this->path;
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

    public function surface()
    {
        $surface = $this->width * $this->height;
        return readablesize($surface, 1000) . 'px';
    }

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

    public function setid($id)
    {
        if (is_string($id)) {
            $this->id = $id;
        }
    }

    public function setpath($path)
    {
        if (strlen($path) < 40 and is_string($path)) {
            $this->path = strip_tags(strtolower($path));
        }
    }

    public function setextension($extension)
    {
        if (strlen($extension) < 7 and is_string($extension)) {
            $this->extension = strip_tags(strtolower($extension));
        }
    }

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
        $timestamp = filemtime($this->getfulldir());
        try {
            $this->date = new DateTimeImmutable("@$timestamp");
        } catch (Exception $e) {
            Logger::warningex($e);
            $this->date = new DateTimeImmutable();
        }
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
        $this->uid = $uid;
    }

    public function setpermissions($permissions)
    {
        $this->permissions = $permissions;
    }
}
