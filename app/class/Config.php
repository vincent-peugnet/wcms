<?php

namespace Wcms;

use DomainException;
use Wcms\Exception\Filesystemexception;

abstract class Config
{
    protected static string $pagetable = 'mystore';
    protected static string $domain = '';
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
    protected static string $defaultcontent = '';
    protected static int $defaultprivacy = 0;
    /** @var string[] $defaulttag */
    protected static array $defaulttag = [];
    protected static string $defaulttemplatebody = '';
    protected static ?string $defaulttemplatecss = null;
    protected static ?string $defaulttemplatejavascript = null;
    protected static bool $defaultnoindex = false;

    protected static string $suffix = "";
    protected static bool $externallinkblank = true;
    protected static bool $internallinkblank = false;
    protected static bool $urllinker = true;
    protected static bool $urlchecker = true;
    protected static bool $deletelinktocache = true;
    protected static bool $titlefromalt = false;
    protected static string $homepage = self::HOMEPAGE_DEFAULT;
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

    protected static string $helpbutton = '';

    /** @var string $lang Default string for pages */
    protected static $lang = "en";

    /** Page version during creation */
    protected static int $pageversion = Page::V1;

    /** Indicate if img should have loading="lazy" attribute */
    protected static bool $lazyloadimg = true;

    /** Global cache duration in seconds. Default is one week */
    protected static int $cachettl = 604800;

    /** LDAP auth */
    protected static string $ldapserver = '';
    protected static string $ldaptree = '';
    protected static string $ldapu = '';
    protected static int $ldapuserlevel = 0;

    public const LANG_MIN = 2;
    public const LANG_MAX = 16;

    public const SUFFIX_MAX = 128;

    public const HOMEPAGE_DEFAULT = 'default';
    public const HOMEPAGE_REDIRECT = 'redirect';

    public const HOMEPAGE = [
        self::HOMEPAGE_DEFAULT,
        self::HOMEPAGE_REDIRECT,
    ];

    public const SECRET_KEY_MIN = 16;
    public const SECRET_KEY_MAX = 128;


    // _______________________________________ F U N _______________________________________


    /**
     * @param object|array<string, mixed> $datas
     */
    public static function hydrate($datas): void
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

    /**
     * @throws Filesystemexception          If file cant be saved
     */
    public static function savejson(): bool
    {
        $json = self::tojson();

        return Fs::writefile(Model::CONFIG_FILE, $json);
    }


    protected static function tojson(): string
    {
        $arr = get_class_vars(get_class());
        // get_class_vars returns default values, we need to update each of them with the current one
        foreach ($arr as $key => $value) {
            $arr[$key] = self::$$key;
        }
        $json = json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_LINE_TERMINATORS);
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
    public static function getdomain(): void
    {
        self::$domain = 'http' . (self::issecure() ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
    }

    /**
     * Generate full url address where W is installed
     * @return string url address finished by a slash "/"
     */
    public static function url(bool $endslash = true): string
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

    public static function pagetable(): string
    {
        return self::$pagetable;
    }

    public static function domain(): string
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

    public static function alerttitle(): string
    {
        return self::$alerttitle;
    }

    public static function alertlink(): string
    {
        return self::$alertlink;
    }

    public static function alertlinktext(): string
    {
        return self::$alertlinktext;
    }

    public static function existnot(): string
    {
        return self::$existnot;
    }

    public static function private(): string
    {
        return self::$private;
    }

    public static function notpublished(): string
    {
        return self::$notpublished;
    }

    public static function existnotpass(): bool
    {
        return self::$existnotpass;
    }

    public static function privatepass(): bool
    {
        return self::$privatepass;
    }

    public static function notpublishedpass(): bool
    {
        return self::$notpublishedpass;
    }

    public static function alertcss(): bool
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

    /**
     * @return string[]|string
     *
     * @throws DomainException if given option is invalid
     */
    public static function defaulttag(string $option = 'array')
    {
        if ($option == 'string') {
            return implode(", ", self::$defaulttag);
        } elseif ($option == 'array') {
            return self::$defaulttag;
        }
        throw new DomainException('invalid option given');
    }

    public static function defaulttemplatebody(): string
    {
        return self::$defaulttemplatebody;
    }

    public static function defaulttemplatecss(): ?string
    {
        return self::$defaulttemplatecss;
    }

    public static function defaulttemplatejavascript(): ?string
    {
        return self::$defaulttemplatejavascript;
    }

    public static function defaultnoindex(): bool
    {
        return self::$defaultnoindex;
    }

    public static function defaultfavicon(): string
    {
        return self::$defaultfavicon;
    }

    public static function defaultthumbnail(): string
    {
        return self::$defaultthumbnail;
    }

    public static function defaultcontent(): string
    {
        return self::$defaultcontent;
    }

    public static function suffix(): string
    {
        return self::$suffix;
    }

    public static function externallinkblank(): bool
    {
        return self::$externallinkblank;
    }

    public static function internallinkblank(): bool
    {
        return self::$internallinkblank;
    }

    public static function urllinker(): bool
    {
        return self::$urllinker;
    }

    public static function urlchecker(): bool
    {
        return self::$urlchecker;
    }

    public static function deletelinktocache(): bool
    {
        return self::$deletelinktocache;
    }

    public static function titlefromalt(): bool
    {
        return self::$titlefromalt;
    }

    public static function defaultprivacy(): int
    {
        return self::$defaultprivacy;
    }

    public static function homepage(): string
    {
        return self::$homepage;
    }

    public static function homeredirect(): ?string
    {
        return self::$homeredirect;
    }

    public static function theme(): string
    {
        return self::$theme;
    }

    public static function secretkey(): ?string
    {
        return self::$secretkey;
    }

    public static function sentrydsn(): string
    {
        return self::$sentrydsn;
    }

    /**
     * @return string|false
     */
    public static function debug()
    {
        return self::$debug;
    }

    public static function markdownhardwrap(): bool
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

    public static function helpbutton(): string
    {
        return self::$helpbutton;
    }

    public static function pageversion(): int
    {
        return self::$pageversion;
    }

    public static function lazyloadimg(): bool
    {
        return self::$lazyloadimg;
    }

    public static function cachettl(): int
    {
        return self::$cachettl;
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

    public static function setpagetable(string $pagetable): void
    {
        self::$pagetable = strip_tags($pagetable);
    }

    public static function setdomain(string $domain): void
    {
        self::$domain = strip_tags(strtolower($domain));
    }

    public static function setsecure(bool $secure): void
    {
        self::$secure = $secure;
    }

    public static function setbasepath(string $basepath): void
    {
        self::$basepath = $basepath;
    }

    public static function setalerttitle(string $alerttitle): void
    {
        self::$alerttitle = strip_tags($alerttitle);
    }

    public static function setalertlink(string $alertlink): void
    {
        self::$alertlink = Model::idclean($alertlink);
    }

    public static function setalertlinktext(string $alertlinktext): void
    {
        self::$alertlinktext = strip_tags($alertlinktext);
    }

    public static function setexistnot(string $existnot): void
    {
        self::$existnot = strip_tags($existnot);
    }

    public static function setprivate(string $private): void
    {
        self::$private = strip_tags($private);
    }

    public static function setnotpublished(string $notpublished): void
    {
        self::$notpublished = strip_tags($notpublished);
    }

    public static function setexistnotpass(bool $existnotpass): void
    {
        self::$existnotpass = $existnotpass;
    }

    public static function setprivatepass(bool $privatepass): void
    {
        self::$privatepass = $privatepass;
    }

    public static function setnotpublishedpass(bool $notpublishedpass): void
    {
        self::$notpublishedpass = $notpublishedpass;
    }

    public static function setalertcss(bool $alertcss): void
    {
        self::$alertcss = $alertcss;
    }

    /**
     * @deprecated Used to convert old Config version. Save
     * `defaultbody` param as `defaultv1body`.
     */
    public static function setdefaultbody(string $defaultbody): void
    {
        self::$defaultv1body = crlf2lf($defaultbody);
    }

    public static function setdefaultv1body(string $defaultbody): void
    {
        self::$defaultv1body = crlf2lf($defaultbody);
    }

    public static function setdefaultv2body(string $defaultbody): void
    {
        self::$defaultv2body = crlf2lf($defaultbody);
    }

    /**
     * @param string[]|string $tag
     */
    public static function setdefaulttag($tag): void
    {
        if (is_string($tag) && strlen($tag) < Page::LENGTH_SHORT_TEXT) {
            $tag = Page::tagtoarray($tag);
        }
        if (is_array($tag)) {
            $tag = array_map(function ($id) {
                return Model::idclean($id);
            }, $tag);
            self::$defaulttag = array_unique(array_filter($tag));
            natsort(self::$defaulttag);
        }
    }

    public static function setdefaulttemplatebody(string $templatebody): void
    {
        self::$defaulttemplatebody = $templatebody;
    }

    public static function setdefaulttemplatecss(?string $templatecss): void
    {
        if ($templatecss === '%') {
            self::$defaulttemplatecss = null;
        } else {
            self::$defaulttemplatecss = $templatecss;
        }
    }

    public static function setdefaulttemplatejavascript(?string $templatejavascript): void
    {
        if ($templatejavascript === '%') {
            self::$defaulttemplatejavascript = null;
        } else {
            self::$defaulttemplatejavascript = $templatejavascript;
        }
    }

    public static function setdefaultnoindex(bool $defaultnoindex): void
    {
        self::$defaultnoindex = $defaultnoindex;
    }

    public static function setdefaultfavicon(string $defaultfavicon): void
    {
        self::$defaultfavicon = $defaultfavicon;
    }

    public static function setdefaultthumbnail(string $defaultthumbnail): void
    {
        self::$defaultthumbnail = $defaultthumbnail;
    }

    public static function setdefaultcontent(string $content): void
    {
        if (strlen($content) < Page::LENGTH_LONG_TEXT) {
            self::$defaultcontent = $content;
        }
    }

    public static function setsuffix(string $suffix): void
    {
        if (strlen($suffix) <= self::SUFFIX_MAX) {
            self::$suffix = strip_tags($suffix);
        }
    }

    public static function setexternallinkblank(bool $externallinkblank): void
    {
        self::$externallinkblank = $externallinkblank;
    }

    public static function setinternallinkblank(bool $internallinkblank): void
    {
        self::$internallinkblank = $internallinkblank;
    }

    public static function seturllinker(bool $urllinker): void
    {
        self::$urllinker = $urllinker;
    }

    public static function seturlchecker(bool $urlchecker): void
    {
        self::$urlchecker = $urlchecker;
    }

    public static function setdeletelinktocache(bool $deletelinktocache): void
    {
        self::$deletelinktocache = $deletelinktocache;
    }

    public static function settitlefromalt(bool $titlefromalt): void
    {
        self::$titlefromalt = $titlefromalt;
    }

    public static function setdefaultprivacy(int $defaultprivacy): void
    {
        if ($defaultprivacy >= 0 && $defaultprivacy <= 2) {
            self::$defaultprivacy = $defaultprivacy;
        }
    }

    public static function sethomepage(string $homepage): void
    {
        if (in_array($homepage, self::HOMEPAGE)) {
            self::$homepage = $homepage;
        }
    }

    public static function sethomeredirect(?string $homeredirect): void
    {
        if (is_string($homeredirect) && strlen($homeredirect) > 0) {
            self::$homeredirect = Model::idclean($homeredirect);
        } else {
            self::$homeredirect = null;
        }
    }

    public static function settheme(string $theme): void
    {
        if (file_exists(Model::THEME_DIR . $theme)) {
            self::$theme = $theme;
        }
    }

    public static function setsecretkey(string $secretkey): void
    {
        $stripedsecretkey = strip_tags($secretkey);
        if ($stripedsecretkey === $secretkey) {
            $length = strlen($secretkey);
            if ($length < self::SECRET_KEY_MAX && $length > self::SECRET_KEY_MIN) {
                self::$secretkey = $secretkey;
            }
        }
    }

    public static function setsentrydsn(string $sentrydsn): void
    {
        self::$sentrydsn = $sentrydsn;
    }

    /**
     * @param string|false $debug
     */
    public static function setdebug($debug): void
    {

        self::$debug = $debug;
    }

    public static function setmarkdownhardwrap(bool $markdownhardwrap): void
    {
        self::$markdownhardwrap = $markdownhardwrap;
    }

    public static function setlang(string $lang): void
    {
        self::$lang = mb_substr(strip_tags($lang), 0, self::LANG_MAX);
    }

    public static function sethtmltag(bool $htmltag): void
    {
        self::$htmltag = $htmltag;
    }

    public static function setdisablejavascript(bool $disablejavascript): void
    {
        self::$disablejavascript = $disablejavascript;
    }

    public static function sethelpbutton(string $url): void
    {
        if (strlen($url) < Item::LENGTH_SHORT_TEXT) {
            self::$helpbutton = strip_tags(trim($url));
        }
    }

    public static function setpageversion(int $pageversion): void
    {
        if (key_exists($pageversion, Page::VERSIONS)) {
            self::$pageversion = $pageversion;
        }
    }

    public static function setlazyloadimg(bool $lazyloadimg): bool
    {
        return self::$lazyloadimg = $lazyloadimg;
    }

    public static function setcachettl(int $cachettl): void
    {
        if ($cachettl >= -1 && $cachettl <= Model::MAX_CACHE_TTL) {
            self::$cachettl = $cachettl;
        }
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
