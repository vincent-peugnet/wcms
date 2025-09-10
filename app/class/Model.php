<?php

namespace Wcms;

abstract class Model
{
    public const CONFIG_FILE = 'config.json';
    public const MAN_FILE = 'MANUAL.md';
    public const MAN_API_FILE = 'API.md';
    public const MAN_RENDER_DIR = 'assets/manual/';
    public const MAN_API_RENDER_DIR = 'assets/manual/api/';
    public const ASSETS_CSS_DIR = 'assets/css/';
    public const ASSETS_ATOM_DIR = 'assets/atom/';
    public const COLORS_FILE = self::ASSETS_CSS_DIR . 'tagcolors.css';
    public const TAGS_FILE = self::DATABASE_DIR . 'tags.json';
    public const URLS_FILE = self::DATABASE_DIR . 'urls.json';
    public const THEME_DIR = self::ASSETS_CSS_DIR . 'theme/';
    public const JS_DIR = 'assets/js/';
    public const JS_TAGLIST_FILE = self::JS_DIR . 'taglist.js';
    public const ICONS_DIR = 'assets/icons/';
    public const MEDIA_DIR = 'media/';
    public const FAVICON_DIR = self::MEDIA_DIR . 'favicon/';
    public const THUMBNAIL_DIR = self::MEDIA_DIR . 'thumbnail/';
    public const FONT_DIR = self::MEDIA_DIR . 'fonts/';
    public const CSS_DIR = self::MEDIA_DIR . 'css/';
    public const TEMPLATES_DIR = './app/view/templates/';
    public const ASSETS_RENDER_DIR = 'assets/render/';
    public const HTML_RENDER_DIR = 'render/';
    public const GLOBAL_CSS_FILE = self::CSS_DIR . 'global.css';
    public const FONTS_CSS_FILE = self::CSS_DIR . 'fonts.css';
    public const DATABASE_DIR = './database/';
    public const PAGES_DIR = self::DATABASE_DIR . 'pages/';
    public const ERROR_LOG = 'w_error.log';

    public const FILE_PERMISSION    = 0660;
    public const FOLDER_PERMISSION  = 0770;

    public const METADATAS_NAMES = [
        'favicon' => 'favicon',
        'id' => 'id',
        'download' => 'download',
        'tag' => 'tag',
        'title' => 'title',
        'description' => 'description',
        'linkto' => 'internal links',
        'externallinks' => 'external links',
        'geolocalisation' => 'geolocalisation',
        'datemodif' => 'modification date',
        'datecreation' => 'creation date',
        'date' => 'date',
        'secure' => 'privacy',
        'authors' => 'authors',
        'visitcount' => 'visit',
        'editcount' => 'edit',
        'displaycount' => 'display',
        'version' => 'version',
    ];

    /** Characters that are authorized in item ID */
    public const ID_AUTHORIZED_CHARS         = 'a-z0-9-_';

    /** Maximum database item ID length */
    public const MAX_ID_LENGTH               = 64;

    /** Regex for unauthorized characters in item IDs */
    public const ID_UNAUTHORIZED_CHARS_REGEX = '[^' . self::ID_AUTHORIZED_CHARS . ']';

    /** Regex for database items IDs*/
    public const ID_REGEX                    = '[' . self::ID_AUTHORIZED_CHARS . ']{1,' . self::MAX_ID_LENGTH . '}';

    public const PASSWORD_MIN_LENGTH         = 4;
    public const PASSWORD_MAX_LENGTH         = 128;
    public const MAX_COOKIE_CONSERVATION     = 365;
    public const MAX_QUERY_LENGH             = 512;
    public const MAX_CACHE_TTL               = 1000000000;


    public static function dirtopath(string $dir): string
    {
        $basepath = '';
        if (!empty(Config::basepath())) {
            $basepath = Config::basepath() . '/'  ;
        }
        $dir = str_replace('\\', '/', $dir);
        return '/' . $basepath . $dir;
    }

    public static function renderpath(): string
    {
        return self::dirtopath(Model::ASSETS_RENDER_DIR);
    }

    public static function csspath(): string
    {
        return self::dirtopath(Model::CSS_DIR);
    }

    public static function assetscsspath(): string
    {
        return self::dirtopath(Model::ASSETS_CSS_DIR);
    }

    public static function themepath(): string
    {
        return self::dirtopath(Model::THEME_DIR);
    }

    public static function jspath(): string
    {
        return self::dirtopath(Model::JS_DIR);
    }

    public static function mediapath(): string
    {
        return self::dirtopath(Model::MEDIA_DIR);
    }

    public static function faviconpath(): string
    {
        return self::dirtopath(Model::FAVICON_DIR);
    }

    public static function thumbnailpath(): string
    {
        return self::dirtopath(Model::THUMBNAIL_DIR);
    }

    public static function fontpath(): string
    {
        return self::dirtopath(Model::FONT_DIR);
    }

    public static function iconpath(): string
    {
        return self::dirtopath(Model::ICONS_DIR);
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

            $regex = '%' . self::ID_UNAUTHORIZED_CHARS_REGEX . '%';
            $input = preg_replace($regex, '', strtolower(trim($input)));
            $input = mb_substr($input, 0, $max);
        }
        return $input;
    }

    /**
     * Check compatibility with W identifier syntax
     *
     * @return bool true if valid ID otherwise false
     */
    public static function idcheck(string $id, int $max = self::MAX_ID_LENGTH): bool
    {
        return (
            !((bool) preg_match('%' . self::ID_UNAUTHORIZED_CHARS_REGEX . '%', $id))
            && strlen($id) <= $max
            && strlen($id) > 0
        );
    }
}
