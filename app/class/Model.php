<?php

namespace Wcms;

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
        'info' => 'info',
        'warning' => 'warning',
        'success' => 'success',
        'error' => 'error'
    ];

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

    public const MAX_ID_LENGTH = 64;
    public const PASSWORD_MIN_LENGTH = 4;
    public const PASSWORD_MAX_LENGTH = 64;
    public const MAX_COOKIE_CONSERVATION = 365;
    public const MAX_QUERY_LENGH = 256;

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
     * @param string $type Message Type, can be `info|warning|success|eror`
     */
    public static function sendflashmessage(string $content, string $type = 'info')
    {
        if (!key_exists($type, self::FLASH_MESSAGE_TYPES)) {
            $type = 'info';
        }
        $_SESSION['user' . Config::basepath()]['flashmessages'][] = ['content' => $content, 'type' => $type];
    }
}
