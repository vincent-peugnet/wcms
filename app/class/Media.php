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

    public const IMAGE = array('jpg', 'jpeg', 'gif', 'png');
    public const SOUND = array('mp3', 'flac', 'wav', 'ogg');
    public const VIDEO = array('mp4', 'mov', 'avi', 'mkv');
    public const ARCHIVE = array('zip', 'rar');



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
     *
     * @return string html code
     */
    public function getcode(): string
    {
        switch ($this->type) {
            case 'image':
                $code = '![' . $this->id . '](' . $this->getincludepath() . ')';
                break;
                
            case 'sound':
                    $code = '&lt;audio controls src=&quot;' . $this->getincludepath() . '&quot;&gt;&lt;/audio&gt;';
                break;
                
            case 'video':
                $src = $this->getincludepath();
                $ext = $this->extension;
                $code = '&lt;video controls=&quot;&quot;&gt;';
                $code .= '&lt;source src=&quot;' . $src . '&quot; type="video/' . $ext . '&quot;&gt;&lt;/video&gt;';
                break;

            default:
                    $code = '[' . $this->id . '](' . $this->getincludepath() . ')';
                break;
        }
            
        return $code;
    }

    public function getsymbol()
    {
        switch ($this->type) {
            case 'image':
                $symbol = "🖼";
                break;
            
            case 'sound':
                $symbol = "🎵";
                break;
            
            case 'video':
                $symbol = "🎞";
                break;
                
            case 'document':
                $symbol = "📓";
                break;
            
            case 'archive':
                $symbol = "🗜";
                break;
                    
            case 'code':
                $symbol = "📄";
                break;
                            
            default:
                $symbol = "🎲";
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
        if (!empty($this->extension) && isset(Model::MEDIA_EXT[$this->extension])) {
            $this->type = Model::MEDIA_EXT[$this->extension];
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
        } catch (\Throwable $th) {
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
