<?php

namespace Wcms;

abstract class Model
{
    public const CONFIG_FILE = 'config.json';
    public const MAN_FILE = 'MANUAL.md';
    public const MAN_RENDER_DIR = 'assets/manual/';
    public const ASSETS_CSS_DIR = 'assets/css/';
    public const ASSETS_ATOM_DIR = 'assets/atom/';
    public const COLORS_FILE = self::ASSETS_CSS_DIR . 'tagcolors.css';
    public const TAGS_FILE = self::DATABASE_DIR . 'tags.json';
    public const THEME_DIR = self::ASSETS_CSS_DIR . 'theme/';
    public const JS_DIR = 'assets/js/';
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

    public const FILE_PERMISSION    = 0660;
    public const FOLDER_PERMISSION  = 0770;

    public const LIST_STYLES = [
        'list' => 'list',
        'card' => 'card'
    ];

    public const METADATAS_NAMES = [
        'favicon' => 'favicon',
        'id' => 'id',
        'download' => 'download',
        'tag' => 'tag',
        'title' => 'title',
        'description' => 'description',
        'linkto' => 'linkto',
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

    public const ID_REGEX                   = "%[^a-z0-9-_]%";
    public const MAX_ID_LENGTH              = 64;
    public const PASSWORD_MIN_LENGTH        = 4;
    public const PASSWORD_MAX_LENGTH        = 128;
    public const MAX_COOKIE_CONSERVATION    = 365;
    public const MAX_QUERY_LENGH            = 512;


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
        return self::dirtopath(Model::ASSETS_RENDER_DIR);
    }

    public static function csspath()
    {
        return self::dirtopath(Model::CSS_DIR);
    }

    public static function assetscsspath()
    {
        return self::dirtopath(Model::ASSETS_CSS_DIR);
    }

    public static function themepath()
    {
        return self::dirtopath(Model::THEME_DIR);
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

            $input = preg_replace(static::ID_REGEX, '', strtolower(trim($input)));
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
            !((bool) preg_match(static::ID_REGEX, $id))
            && strlen($id) <= $max
            && strlen($id) > 0
        );
    }
}
