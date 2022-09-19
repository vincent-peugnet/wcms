<?php

namespace Wcms;

use RuntimeException;

abstract class Model
{
    public const CONFIG_FILE = 'config.json';
    public const MAN_FILE = 'MANUAL.md';
    public const ASSETS_CSS_DIR = 'assets/css/';
    public const COLORS_FILE = self::ASSETS_CSS_DIR . 'tagcolors.css';
    public const JS_DIR = 'assets/js/';
    public const ICONS_DIR = 'assets/icons/';
    public const MEDIA_DIR = 'media/';
    public const FAVICON_DIR = self::MEDIA_DIR . 'favicon/';
    public const THUMBNAIL_DIR = self::MEDIA_DIR . 'thumbnail/';
    public const FONT_DIR = self::MEDIA_DIR . 'fonts/';
    public const CSS_DIR = self::MEDIA_DIR . 'css/';
    public const TEMPLATES_DIR = './app/view/templates/';
    public const RENDER_DIR = 'assets/render/';
    public const HTML_RENDER_DIR = 'render/';
    public const GLOBAL_CSS_FILE = self::CSS_DIR . 'global.css';
    public const DATABASE_DIR = './database/';
    public const PAGES_DIR = self::DATABASE_DIR . 'pages/';

    public const FILE_PERMISSION    = 0660;
    public const FOLDER_PERMISSION  = 0770;

    public const MEDIA_SORTBY = [
        'id' => 'id',
        'size' => 'size',
        'type' => 'type',
        'date' => 'date',
        'extension' => 'extension'
    ];

    public const MAP_LAYOUTS = [
        'cose' => 'cose',
        'cose-bilkent' => 'cose-bilkent',
        'circle' => 'circle',
        'breadthfirst' => 'breadthfirst',
        'concentric' => 'concentric',
        'grid' => 'grid',
        'random' => 'random',
    ];


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

    public const BOOKMARK_ICONS = [
        '⭐️', '🖤', '🏴', '👍', '📌', '💡', '🌘', '☂️', '✈️', '🚲', '💾', '💿', '💎', '🎞', ' ⚒', '💊', '📜',
        '📒', '🔓', '🌡', '☎️', '✝️', '☢️', '✅', '🌐', '🌍', '✳️', '🏴', '😎', '👻', '💩', '⚡️', '🍸', '🔍', '📦',
        '🍴', '⚽️', '🏭', '🚀', '⚓️', '🔒'
    ];

    public const LIST_STYLES = [
        'list' => 'list',
        'card' => 'card'
    ];

    public const FLASH_MESSAGE_TYPES = [
        self::FLASH_INFO    => 1,
        self::FLASH_WARNING => 2,
        self::FLASH_SUCCESS => 3,
        self::FLASH_ERROR   => 4,
    ];

    public const FLASH_INFO     = 'info';
    public const FLASH_WARNING  = 'warning';
    public const FLASH_SUCCESS  = 'success';
    public const FLASH_ERROR    = 'error';

    public const SECURE_LEVELS = [
        0 => 'public',
        1 => 'private',
        2 => 'not_published'
    ];

    public const COLUMNS = [
        'id',
        'favicon',
        'title',
        'description',
        'tag',
        'date',
        'datemodif',
        'datecreation',
        'secure',
        'authors',
        'linkto',
        'visitcount',
        'affcount',
        'editcount'
    ];

    public const TEXT_ELEMENTS = ['header', 'nav', 'main', 'aside', 'footer'];

    public const ID_REGEX                   = "%[^a-z0-9-_]%";
    public const MAX_ID_LENGTH              = 64;
    public const PASSWORD_MIN_LENGTH        = 4;
    public const PASSWORD_MAX_LENGTH        = 128;
    public const MAX_COOKIE_CONSERVATION    = 365;
    public const MAX_QUERY_LENGH            = 256;

    /** RENDER OPTIONS   */
    // add class in html element indicating from witch page the content come.
    public const RENDER_CLASS_ORIGIN = false;
    // render empty CONTENT element as empty html element, if set to false, render html comment
    public const RENDER_EMPTY_ELEMENT = false;


    /** CONFIG OPTIONS */
    public const HOMEPAGE = ['default', 'search', 'redirect'];

    public static function dirtopath($dir)
    {
        $basepath = '';
        if (!empty(Config::basepath())) {
            $basepath = Config::basepath() . '/'  ;
        }
        $dir = str_replace('\\', '/', $dir);
        return '/' . $basepath . $dir;
    }

    public static function renderpath()
    {
        return self::dirtopath(Model::RENDER_DIR);
    }

    public static function csspath()
    {
        return self::dirtopath(Model::CSS_DIR);
    }

    public static function assetscsspath()
    {
        return self::dirtopath(Model::ASSETS_CSS_DIR);
    }

    public static function jspath()
    {
        return self::dirtopath(Model::JS_DIR);
    }

    public static function mediapath()
    {
        return self::dirtopath(Model::MEDIA_DIR);
    }

    public static function faviconpath()
    {
        return self::dirtopath(Model::FAVICON_DIR);
    }

    public static function thumbnailpath()
    {
        return self::dirtopath(Model::THUMBNAIL_DIR);
    }

    public static function fontpath()
    {
        return self::dirtopath(Model::FONT_DIR);
    }

    public static function iconpath()
    {
        return self::dirtopath(Model::ICONS_DIR);
    }

    /**
     *
     */
    public static function mediatypes()
    {
        return array_unique(array_values(self::MEDIA_EXT));
    }

    /**
     * Read then empty session to get flash messages
     *
     * @return array ordered array containing array with content and type as keys or empty array
     */
    public static function getflashmessages(): array
    {
        if (!empty($_SESSION['user' . Config::basepath()]['flashmessages'])) {
            $flashmessage = $_SESSION['user' . Config::basepath()]['flashmessages'];
            $_SESSION['user' . Config::basepath()]['flashmessages'] = [];
            if (is_array($flashmessage)) {
                return $flashmessage;
            } else {
                return [];
            }
        } else {
            return [];
        }
    }

    /**
     * Add a message to flash message list
     *
     * @param string $content The message content
     * @param string $type Message Type, can be `info|warning|success|error`
     */
    public static function sendflashmessage(string $content, string $type = self::FLASH_INFO): void
    {
        if (!key_exists($type, self::FLASH_MESSAGE_TYPES)) {
            $type = self::FLASH_INFO;
        }
        $_SESSION['user' . Config::basepath()]['flashmessages'][] = ['content' => $content, 'type' => $type];
    }

    /**
     * @param string $filename
     * @param mixed $data
     * @param int $permissions optionnal permission in octal format (a zero before three numbers)
     *
     * @return bool False if file is not writen, otherwise true (even if permission warning)
     *
     * Send flash messages:
     * - error when problem writing the file
     * - warning when problem with permissions
     */
    public static function writefile(string $filename, $data, int $permissions = self::FILE_PERMISSION): bool
    {
        if ($permissions < 0600 || $permissions > 0777) {
            self::sendflashmessage(
                sprintf("Invalid permissions 0%o, using default: 0%o", $permissions, self::FILE_PERMISSION),
                'warning'
            );
            $permissions = self::FILE_PERMISSION;
        }
        try {
            file_put_content_chmod($filename, $data, $permissions);
        } catch (Chmodexception $e) {
            Logger::warning($e->getMessage());
            self::sendflashmessage($e->getMessage(), 'warning');
        } catch (Ioexception $e) {
            Logger::error($e->getMessage());
            self::sendflashmessage($e->getMessage(), 'error');
            return false;
        }
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
     * Clean string from characters outside authorized ID characters and troncate it
     * @param string $input
     * @param int $max minmum input length to trucate id
     * @return string output formated id
     */
    public static function idclean(string $input, int $max = self::MAX_ID_LENGTH): string
    {
        if (!self::idcheck($input, $max)) {
            $input = urldecode($input);
            $input = trim($input);

            $search =  ['é', 'à', 'è', 'ç', 'ù', 'ü', 'ï', 'î', ' '];
            $replace = ['e', 'a', 'e', 'c', 'u', 'u', 'i', 'i', '-'];
            $input = str_replace($search, $replace, $input);

            $input = preg_replace(self::ID_REGEX, '', strtolower(trim($input)));
            $input = substr($input, 0, $max);
        }
        return $input;
    }

    /**
     * @return bool true if valid ID otherwise false
     */
    public static function idcheck(string $id, int $max = self::MAX_ID_LENGTH): bool
    {
        return (
            !((bool) preg_match(self::ID_REGEX, $id))
            && strlen($id) <= $max
        );
    }
}
