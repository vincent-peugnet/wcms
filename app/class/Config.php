<?php

namespace Wcms;

use Wcms\Exception\Filesystemexception;

abstract class Config
{
    protected static $pagetable = 'mystore';
    protected static $domain = '';
    protected static bool $secure = true;
    protected static string $basepath = '';
    protected static string $alerttitle = '';
    protected static string $alertlink = '';
    protected static string $alertlinktext = '';
    protected static string $existnot = 'This page does not exist yet';
    protected static string $private = 'This page is private';
    protected static string $notpublished = 'This page is not published';
    protected static bool $existnotpass = false;
    protected static bool $privatepass = false;
    protected static bool $notpublishedpass = false;
    protected static bool $alertcss = false;
    protected static string $defaultv1body = "%HEADER%\n\n%NAV%\n\n%ASIDE%\n\n%MAIN%\n\n%FOOTER%";
    protected static string $defaultv2body = "%CONTENT%";
    protected static string $defaultfavicon = '';
    protected static string $defaultthumbnail = '';
    protected static string $suffix = "";
    protected static bool $externallinkblank = true;
    protected static bool $internallinkblank = false;
    protected static bool $urllinker = true;
    protected static bool $deletelinktocache = true;
    protected static int $defaultprivacy = 0;
    protected static string $homepage = 'default';
    protected static ?string $homeredirect = null;
    protected static string $theme = 'default.css';
    protected static ?string $secretkey = null;
    protected static string $sentrydsn = '';

    /** @var string|false $debug */
    protected static $debug = false;

    /** Database config */
    protected static bool $markdownhardwrap = true;

    /** @var bool BODY content inclusion have HTML tags printed around them */
    protected static bool $htmltag = true;


    /** Site config */

    /** @var bool $disablejavascript */
    protected static bool $disablejavascript = false;
    /** @var string $lang Default string for pages */
    protected static $lang = "en";

    /** Page version during creation */
    protected static int $pageversion = Page::V1;

    /** Indicate if img should have loading="lazy" attribute */
    protected static bool $lazyloadimg = true;

    /** LDAP auth */
    protected static string $ldapserver = '';
    protected static string $ldaptree = '';
    protected static string $ldapu = '';
    protected static int $ldapuserlevel = 0;

    public const LANG_MIN = 2;
    public const LANG_MAX = 16;

    public const SUFFIX_MAX = 128;

    public const HOMEPAGE = ['default', 'redirect'];

    public const SECRET_KEY_MIN = 16;
    public const SECRET_KEY_MAX = 128;


    // _______________________________________ F U N _______________________________________



    public static function hydrate(array $datas)
    {
        foreach ($datas as $key => $value) {
            $method = 'set' . $key;
            if (method_exists(get_called_class(), $method)) {
                self::$method($value);
            }
        }
    }

    public static function readconfig(): bool
    {
        if (file_exists(Model::CONFIG_FILE)) {
            $current = file_get_contents(Model::CONFIG_FILE);
            $datas = json_decode($current, true);
            self::hydrate($datas);
            // Setup old config file to user page version 1
            if (isset($datas['pageaversion'])) {
                self::$pageversion = Page::V1;
            }
            return true;
        } else {
            return false;
        }
    }

    public static function createconfig(array $datas)
    {
        self::hydrate($datas);
    }

    /**
     * @throws Filesystemexception          If file cant be saved
     */
    public static function savejson()
    {
        $json = self::tojson();

        return Fs::writefile(Model::CONFIG_FILE, $json);
    }


    public static function tojson()
    {
        $arr = get_class_vars(get_class());
        // get_class_vars returns default values, we need to update each of them with the current one
        foreach ($arr as $key => $value) {
            $arr[$key] = self::$$key;
        }
        $json = json_encode($arr, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT | JSON_UNESCAPED_LINE_TERMINATORS);
        return $json;
    }

    /**
     * Check if basepath is correct
     */
    public static function checkbasepath(): bool
    {
        if (str_starts_with(self::$basepath, '/') || str_ends_with(self::$basepath, '/')) {
            return false;
        }
        $path = $_SERVER['DOCUMENT_ROOT'] . '/' . self::$basepath . '/' .  Model::CONFIG_FILE;
        return (file_exists($path));
    }

    /**
     * Calculate Domain name
     */
    public static function getdomain()
    {
        self::$domain = 'http' . (self::issecure() ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
    }

    /**
     * Generate full url address where W is installed
     * @return string url address finished by a slash "/"
     */
    public static function url($endslash = true): string
    {
        return self::$domain . (!empty(self::$basepath) ? '/' . self::$basepath : "") . ($endslash ? '/' : '');
    }

    /**
     * @return bool                         Indicate if ldap is configured. (all 3 params are not empty)
     */
    public static function isldap(): bool
    {
        return (
            !empty(self::$ldapserver)
            && !empty(self::$ldaptree)
            && !empty(self::$ldapu)
        );
    }

    // ________________________________________ G E T _______________________________________

    public static function pagetable()
    {
        return self::$pagetable;
    }

    public static function domain()
    {
        return self::$domain;
    }

    public static function issecure(): bool
    {
        return self::$secure;
    }

    /**
     * @return string                       Basepath without trailing slash
     */
    public static function basepath(): string
    {
        return self::$basepath;
    }
    public static function alerttitle()
    {
        return self::$alerttitle;
    }

    public static function alertlink()
    {
        return self::$alertlink;
    }

    public static function alertlinktext()
    {
        return self::$alertlinktext;
    }

    public static function existnot()
    {
        return self::$existnot;
    }

    public static function private()
    {
        return self::$private;
    }

    public static function notpublished()
    {
        return self::$notpublished;
    }

    public static function existnotpass()
    {
        return self::$existnotpass;
    }

    public static function privatepass()
    {
        return self::$privatepass;
    }

    public static function notpublishedpass()
    {
        return self::$notpublishedpass;
    }

    public static function alertcss()
    {
        return self::$alertcss;
    }

    /**
     * @return string Default BODY corrsponding to current Config's page version
     */
    public static function defaultbody(): string
    {
        $fn = 'defaultv' . self::$pageversion . 'body';
        return self::$$fn;
    }

    public static function defaultv1body(): string
    {
        return self::$defaultv1body;
    }

    public static function defaultv2body(): string
    {
        return self::$defaultv2body;
    }

    public static function defaultfavicon()
    {
        return self::$defaultfavicon;
    }

    public static function defaultthumbnail()
    {
        return self::$defaultthumbnail;
    }

    public static function suffix(): string
    {
        return self::$suffix;
    }

    public static function externallinkblank()
    {
        return self::$externallinkblank;
    }

    public static function internallinkblank()
    {
        return self::$internallinkblank;
    }

    public static function urllinker(): bool
    {
        return self::$urllinker;
    }

    public static function deletelinktocache(): bool
    {
        return self::$deletelinktocache;
    }

    public static function defaultprivacy()
    {
        return self::$defaultprivacy;
    }

    public static function homepage()
    {
        return self::$homepage;
    }

    public static function homeredirect()
    {
        return self::$homeredirect;
    }

    public static function theme()
    {
        return self::$theme;
    }

    public static function secretkey()
    {
        return self::$secretkey;
    }

    public static function sentrydsn()
    {
        return self::$sentrydsn;
    }

    public static function debug()
    {
        return self::$debug;
    }

    public static function markdownhardwrap()
    {
        return self::$markdownhardwrap;
    }

    public static function lang(): string
    {
        return self::$lang;
    }

    public static function htmltag(): bool
    {
        return self::$htmltag;
    }

    public static function disablejavascript(): bool
    {
        return self::$disablejavascript;
    }

    public static function pageversion(): int
    {
        return self::$pageversion;
    }

    public static function lazyloadimg(): bool
    {
        return self::$lazyloadimg;
    }

    public static function ldapserver(): string
    {
        return self::$ldapserver;
    }

    public static function ldaptree(): string
    {
        return self::$ldaptree;
    }

    public static function ldapu(): string
    {
        return self::$ldapu;
    }

    public static function ldapuserlevel(): int
    {
        return self::$ldapuserlevel;
    }


    // __________________________________________ S E T ______________________________________

    public static function setpagetable($pagetable)
    {
        self::$pagetable = strip_tags($pagetable);
    }

    public static function setdomain($domain)
    {
        self::$domain = strip_tags(strtolower($domain));
    }

    public static function setsecure($secure)
    {
        self::$secure = boolval($secure);
    }

    public static function setbasepath($basepath)
    {
        self::$basepath = strip_tags($basepath);
    }

    public static function setalerttitle($alerttitle)
    {
        if (is_string($alerttitle)) {
            self::$alerttitle = strip_tags($alerttitle);
        }
    }

    public static function setalertlink($alertlink)
    {
        if (is_string($alertlink)) {
            self::$alertlink = Model::idclean($alertlink);
        }
    }

    public static function setalertlinktext($alertlinktext)
    {
        if (is_string($alertlinktext)) {
            self::$alertlinktext = strip_tags($alertlinktext);
        }
    }

    public static function setexistnot($existnot)
    {
        if (is_string($existnot)) {
            self::$existnot = strip_tags($existnot);
        }
    }

    public static function setprivate($private)
    {
        if (is_string($private)) {
            self::$private = strip_tags($private);
        }
    }

    public static function setnotpublished($notpublished)
    {
        if (is_string($notpublished)) {
            self::$notpublished = strip_tags($notpublished);
        }
    }

    public static function setexistnotpass($existnotpass)
    {
        self::$existnotpass = boolval($existnotpass);
    }

    public static function setprivatepass($privatepass)
    {
        self::$privatepass = boolval($privatepass);
    }

    public static function setnotpublishedpass($notpublishedpass)
    {
        self::$notpublishedpass = boolval($notpublishedpass);
    }

    public static function setalertcss($alertcss)
    {
        self::$alertcss = boolval($alertcss);
    }

    /**
     * Used to convert old Config version. Save
     * `defaultbody` param as `defaultv1body`.
     */
    public static function setdefaultbody($defaultbody)
    {
        if (is_string($defaultbody)) {
            $defaultbody = crlf2lf($defaultbody);
            self::$defaultv1body = $defaultbody;
        }
    }

    public static function setdefaultv1body($defaultbody)
    {
        if (is_string($defaultbody)) {
            $defaultbody = crlf2lf($defaultbody);
            self::$defaultv1body = $defaultbody;
        }
    }

    public static function setdefaultv2body($defaultbody)
    {
        if (is_string($defaultbody)) {
            $defaultbody = crlf2lf($defaultbody);
            self::$defaultv2body = $defaultbody;
        }
    }

    public static function setdefaultfavicon($defaultfavicon)
    {
        if (is_string($defaultfavicon)) {
            self::$defaultfavicon = $defaultfavicon;
        }
    }

    public static function setdefaultthumbnail($defaultthumbnail)
    {
        if (is_string($defaultthumbnail)) {
            self::$defaultthumbnail = $defaultthumbnail;
        }
    }

    public static function setsuffix($suffix)
    {
        if (is_string($suffix) && strlen($suffix) <= self::SUFFIX_MAX) {
            self::$suffix = strip_tags($suffix);
        }
    }

    public static function setexternallinkblank($externallinkblank)
    {
        self::$externallinkblank = boolval($externallinkblank);
    }

    public static function setinternallinkblank($internallinkblank)
    {
        self::$internallinkblank = boolval($internallinkblank);
    }

    public static function seturllinker($urllinker)
    {
        self::$urllinker = boolval($urllinker);
    }

    public static function setdeletelinktocache($deletelinktocache)
    {
        self::$deletelinktocache = boolval($deletelinktocache);
    }

    public static function setdefaultprivacy($defaultprivacy)
    {
        $defaultprivacy = intval($defaultprivacy);
        if ($defaultprivacy >= 0 && $defaultprivacy <= 2) {
            self::$defaultprivacy = $defaultprivacy;
        }
    }

    public static function sethomepage($homepage)
    {
        if (in_array($homepage, self::HOMEPAGE)) {
            self::$homepage = $homepage;
        }
    }

    public static function sethomeredirect($homeredirect)
    {
        if (is_string($homeredirect) && strlen($homeredirect) > 0) {
            self::$homeredirect = Model::idclean($homeredirect);
        } else {
            self::$homeredirect = null;
        }
    }

    public static function settheme($theme)
    {
        if (is_string($theme) && file_exists(Model::THEME_DIR . $theme)) {
            self::$theme = $theme;
        }
    }

    public static function setsecretkey($secretkey)
    {
        if (is_string($secretkey)) {
            $stripedsecretkey = strip_tags($secretkey);
            if ($stripedsecretkey === $secretkey) {
                $length = strlen($secretkey);
                if ($length < self::SECRET_KEY_MAX && $length > self::SECRET_KEY_MIN) {
                    self::$secretkey = $secretkey;
                }
            }
        }
    }

    public static function setsentrydsn($sentrydsn)
    {
        if (is_string($sentrydsn)) {
            self::$sentrydsn = $sentrydsn;
        }
    }

    public static function setdebug($debug)
    {
        if (is_string($debug)) {
            self::$debug = $debug;
        }
    }

    public static function setmarkdownhardwrap($markdownhardwrap)
    {
        self::$markdownhardwrap = boolval($markdownhardwrap);
    }

    public static function setlang(string $lang)
    {
        self::$lang = mb_substr(strip_tags($lang), 0, self::LANG_MAX);
    }

    public static function sethtmltag($htmltag)
    {
        self::$htmltag = boolval($htmltag);
    }

    public static function setdisablejavascript($disablejavascript)
    {
        self::$disablejavascript = boolval($disablejavascript);
    }

    public static function setpageversion($pageversion): void
    {
        if (key_exists($pageversion, Page::VERSIONS)) {
            self::$pageversion = $pageversion;
        }
    }

    public static function setlazyloadimg($lazyloadimg): bool
    {
        return self::$lazyloadimg = boolval($lazyloadimg);
    }

    public static function setldapserver(string $ldapserver): void
    {
        self::$ldapserver = $ldapserver;
    }

    public static function setldaptree(string $ldaptree): void
    {
        self::$ldaptree = $ldaptree;
    }

    public static function setldapu(string $ldapu): void
    {
        self::$ldapu = $ldapu;
    }

    public static function setldapuserlevel(int $ldapuserlevel): void
    {
        if ($ldapuserlevel >= 0 && $ldapuserlevel <= 10) {
            self::$ldapuserlevel = $ldapuserlevel;
        }
    }
}
