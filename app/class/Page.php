<?php

namespace Wcms;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use DomainException;

abstract class Page extends Item
{
    protected ?string $id = null;
    protected string $title = '';
    protected string $description = '';
    protected string $lang = '';
    /** @var string[] $tag */
    protected array $tag = [];
    protected ?float $latitude = null;
    protected ?float $longitude = null;
    protected DateTimeImmutable $date;
    protected DateTimeImmutable $datecreation;
    protected DateTimeImmutable $datemodif;
    protected DateTimeImmutable $daterender;
    protected string $css = '';
    protected string $javascript = '';
    protected string $body = '';
    /** @var string[] $externalcss */
    protected array $externalcss = [];
    protected string $customhead = '';
    protected int $secure = 0;
    protected string $interface = '';
    /** @var string[] $linkto */
    protected array $linkto = [];
    protected string $templatebody = '';
    protected string $templatecss = '';
    protected string $templatejavascript = '';
    protected string $favicon = '';
    protected string $thumbnail = '';
    /** @var string[] $authors */
    protected array $authors = [];
    protected int $displaycount = 0;
    protected int $visitcount = 0;
    protected int $editcount = 0;
    protected int $sleep = 0;
    protected string $redirection = '';
    protected int $refresh = 0;
    protected string $password = '';
    protected bool $postprocessaction = false;

    /** @var array<string, ?bool> $externallinks */
    protected array $externallinks = [];

    protected int $version;

    public const LATITUDE_MIN = -90;
    public const LATITUDE_MAX = 90;
    public const LONGITUDE_MIN = -180;
    public const LONGITUDE_MAX = 180;

    public const V1 = 1;
    public const V2 = 2;

    public const VERSIONS = [
        self::V1 => "version 1",
        self::V2 => "version 2"
    ];

    public const PUBLIC = 0;
    public const PRIVATE = 1;
    public const NOT_PUBLISHED = 2;

    public const SECUREMAX = 2;
    public const TABS = ['css', 'body', 'javascript'];
    public const VAR_DATE = ['date', 'datecreation', 'datemodif', 'daterender'];


// _____________________________________________________ F U N ____________________________________________________

    /**
     * @param array<string, mixed>|object $datas
     */
    public function __construct($datas = [])
    {
        $this->reset();
        $this->hydrate($datas);
    }

    /**
     * Return a list of all class vars name as strings
     *
     * @return string[]
     */
    public static function getclassvars(): array
    {
        return array_keys(get_class_vars(self::class));
    }

    public function reset(): void
    {
        $now = new DateTimeImmutable("now", timezone_open("Europe/Paris"));

        if ($this->id !== null) {
            $this->settitle($this->id);
        }
        $this->setdescription('');
        $this->setlang('');
        $this->settag(Config::defaulttag());
        $this->latitude = null;
        $this->longitude = null;
        $this->setdate($now);
        $this->setdatecreation($now);
        $this->setdatecreation($now);
        $this->setdatemodif($now);
        $this->setdaterender($now);
        $this->setcss('');
        $this->setjavascript('');
        $this->setbody(Config::defaultbody());
        $this->setexternalcss([]);
        $this->setcustomhead('');
        $this->setsecure(Config::defaultprivacy());
        $this->setlinkto([]);
        $this->settemplatebody(Config::defaulttemplatebody());
        $this->settemplatecss(Config::defaulttemplatecss());
        $this->settemplatejavascript(Config::defaulttemplatejs());
        $this->setfavicon('');
        $this->setthumbnail('');
        $this->setauthors([]);
        $this->setvisitcount(0);
        $this->seteditcount(0);
        $this->setdisplaycount(0);
        $this->setsleep(0);
        $this->setredirection('');
        $this->setrefresh(0);
        $this->setpassword('');
        $this->externallinks = [];
        $this->postprocessaction = false;
    }

    public function ispublic(): bool
    {
        return $this->secure === 0;
    }

    public function isprivate(): bool
    {
        return $this->secure === 1;
    }

    public function isnotpublished(): bool
    {
        return $this->secure === 2;
    }

    /**
     * Indicate if the page rendered HTML can be cached by the Web browser client.
     */
    public function canbecached(): bool
    {
        return $this->ispublic() && empty($this->password) && !$this->postprocessaction;
    }

    /**
     * Indicate if Page is geolocated. Meaning it's latitude and longitude are both set (they are not null): void
     */
    public function isgeo(): bool
    {
        return (!is_null($this->latitude) && !is_null($this->longitude));
    }

    /**
     * @return string[]                     Page fields containting content
     */
    public function contents(): array
    {
        return array_diff($this::TABS, Page::TABS);
    }

    /**
     * @return string[]                     Tabs contents to be included in edit page
     */
    public function tabs(): array
    {
        $tabs = [];
        foreach ($this::TABS as $tab) {
            $tabs[$tab] = $this->$tab;
        }
        return $tabs;
    }

    // _____________________________________________________ G E T ____________________________________________________

    public function id(string $type = 'string'): ?string
    {
        return $this->id;
    }

    public function title(string $type = 'string'): string
    {
        if ($type == 'sort') {
            return strtolower($this->title);
        } else {
            return $this->title;
        }
    }

    public function description(string $type = 'string'): string
    {
        if ($type == 'short' && strlen($this->description) > 15) {
                return mb_substr($this->description, 0, 20) . '...';
        } else {
            return $this->description;
        }
    }

    public function lang(): string
    {
        return $this->lang;
    }

    /**
     * @return string[]|int|string
     *
     * @throws DomainException if given option is invalid
     */
    public function tag(string $option = 'array')
    {
        if ($option == 'string') {
            return implode(", ", $this->tag);
        } elseif ($option == 'array') {
            return $this->tag;
        } elseif ($option == 'sort') {
            return count($this->tag);
        }
        throw new DomainException('invalid option given');
    }

    public function latitude(string $option = 'float'): ?float
    {
        return $this->latitude;
    }

    public function longitude(string $option = 'float'): ?float
    {
        return $this->longitude;
    }

    /**
     * @return DateTimeInterface|string
     */
    public function date(string $option = 'date')
    {
        return $this->datetransform('date', $option);
    }

    /**
     * @return DateTimeInterface|string
     */
    public function datecreation(string $option = 'date')
    {
        return $this->datetransform('datecreation', $option);
    }

    /**
     * @return DateTimeInterface|string
     */
    public function datemodif(string $option = 'date')
    {
        return $this->datetransform('datemodif', $option);
    }

    /**
     * @return DateTimeInterface|string
     */
    public function daterender(string $option = 'date')
    {
        return $this->datetransform('daterender', $option);
    }

    public function primary(string $type = ''): string
    {
        return '';
    }

    public function css(string $type = 'string'): string
    {
        return $this->css;
    }

    public function javascript(string $type = 'string'): string
    {
        return $this->javascript;
    }

    public function body(string $type = 'string'): string
    {
        return $this->body;
    }

    /**
     * @return string[]
     */
    public function externalcss(string $type = "array"): array
    {
        return $this->externalcss;
    }

    /**
     * @return string|int
     *
     * @throws DomainException if type is invalid
     */
    public function customhead(string $type = "string")
    {
        if ($type === 'string') {
            return $this->customhead;
        } elseif ($type === 'int') {
            return substr_count($this->customhead, "\n") + 1;
        }
        throw new DomainException('invalid type given');
    }

    /**
     * @return string|int
     */
    public function secure(string $type = 'int')
    {
        if ($type == 'string') {
            return Modelpage::SECURE_LEVELS[$this->secure];
        } else {
            return $this->secure;
        }
    }

    public function interface(string $type = 'string'): string
    {
        return $this->interface;
    }

    /**
     * @return int|string|string[]
     */
    public function linkto(string $option = 'array')
    {
        if ($option == 'array') {
            return $this->linkto;
        } elseif ($option == 'sort') {
            return count($this->linkto);
        } elseif ($option == 'string') {
            return implode(', ', $this->linkto);
        }
        return $this->linkto;
    }

    public function templatebody(string $type = 'string'): string
    {
        return $this->templatebody;
    }

    public function templatecss(string $type = 'string'): string
    {
        return $this->templatecss;
    }

    public function templatejavascript(string $type = 'string'): string
    {
        return $this->templatejavascript;
    }

    public function favicon(string $type = 'string'): string
    {
        return $this->favicon;
    }

    public function thumbnail(string $type = 'string'): string
    {
        return $this->thumbnail;
    }

    /**
     * @return int|string|string[]
     *
     * @throws DomainException in cas of invalid type
     */
    public function authors(string $type = 'array')
    {
        if ($type == 'string') {
            return implode(', ', $this->authors);
        } elseif ($type == 'array') {
            return $this->authors;
        } elseif ($type == 'sort') {
            return count($this->authors);
        }
        throw new DomainException('invalid type given');
    }

    public function displaycount(string $type = 'int'): int
    {
        return $this->displaycount;
    }

    public function visitcount(string $type = 'int'): int
    {
        return $this->visitcount;
    }

    public function editcount(string $type = 'int'): int
    {
        return $this->editcount;
    }

    public function sleep(string $type = 'int'): int
    {
        return $this->sleep;
    }

    public function redirection(string $type = 'string'): string
    {
        return $this->redirection;
    }

    public function refresh(string $type = 'int'): int
    {
        return $this->refresh;
    }

    public function password(string $type = 'string'): string
    {
        return $this->password;
    }

    public function postprocessaction(string $type = 'int'): bool
    {
        return $this->postprocessaction;
    }

    /**
     * @return array<string, ?bool>|int
     */
    public function externallinks(string $option = 'array')
    {
        if ($option === 'sort') {
            return count($this->externallinks);
        }
        return $this->externallinks;
    }

    public function version(string $type = 'int'): int
    {
        return $this->version;
    }




    // _____________________________________________________ S E T ____________________________________________________

    public function setid(string $id): void
    {
        if (is_string($id) && strlen($id) <= Model::MAX_ID_LENGTH) {
            $this->id = strip_tags(strtolower(str_replace(" ", "", $id)));
        }
    }

    public function settitle(string $title): void
    {
        if (strlen($title) < self::LENGTH_SHORT_TEXT and is_string($title)) {
            $this->title = strip_tags(trim($title));
        }
    }

    public function setdescription(string $description): void
    {
        if (strlen($description) < self::LENGTH_SHORT_TEXT and is_string($description)) {
            $this->description = strip_tags(trim($description));
        }
    }

    public function setlang(string $lang): void
    {
        $this->lang = mb_substr(strip_tags($lang), 0, Config::LANG_MAX);
    }

    /**
     * @param string[]|string $tag
     */
    public function settag($tag): void
    {
        if (is_string($tag) && strlen($tag) < self::LENGTH_SHORT_TEXT) {
            $tag = self::tagtoarray($tag);
        }
        if (is_array($tag)) {
            $tag = array_map(function ($id) {
                return Model::idclean($id);
            }, $tag);
            $this->tag = array_unique(array_filter($tag));
            natsort($this->tag);
        }
    }

    /**
     * @param ?float $latitude              Must be `null` or a float between -90 and 90 degres
     */
    public function setlatitude($latitude): void
    {
        if (is_numeric($latitude)) {
            $latitude = floatval($latitude);
            if ($latitude >= self::LATITUDE_MIN && $latitude <= self::LATITUDE_MAX) {
                $this->latitude = $latitude;
            }
        } else {
            $this->latitude = null;
        }
    }

    /**
     * @param ?float $longitude              Must be `null` or a float between -180 and 180 degres
     */
    public function setlongitude($longitude): void
    {
        if (is_numeric($longitude)) {
            $longitude = floatval($longitude);
            if ($longitude >= self::LONGITUDE_MIN && $longitude <= self::LONGITUDE_MAX) {
                $this->longitude = $longitude;
            }
        } else {
            $this->longitude = null;
        }
    }

    /**
     * @param DateTimeImmutable|string $date
     */
    public function setdate($date): void
    {
        if ($date instanceof DateTimeImmutable) {
            $this->date = $date;
        } elseif (is_string($date)) {
            $this->date = DateTimeImmutable::createFromFormat(
                DateTime::RFC3339,
                $date,
                new DateTimeZone('Europe/Paris')
            );
        }
    }

    public function setptime(string $ptime): void
    {
        if (DateTime::createFromFormat('H:i', $ptime) !== false) {
            $time = explode(':', $ptime);
            $this->date = $this->date->setTime(intval($time[0]), intval($time[1]));
        }
    }

    public function setpdate(string $pdate): void
    {
        if (DateTime::createFromFormat('Y-m-d', $pdate) !== false) {
            $date = explode('-', $pdate);
            $this->date = $this->date->setDate(intval($date[0]), intval($date[1]), intval($date[2]));
        }
    }

    /**
     * DateTimeImmutable : set date
     * string ISO8601 : set date
     * true : reset to now
     *
     * @param string|DateTimeImmutable|true $datecreation Set or reset date of creation
     */
    public function setdatecreation($datecreation): void
    {
        if ($datecreation instanceof DateTimeImmutable) {
            $this->datecreation = $datecreation;
        } elseif ($datecreation === true) {
            $this->datecreation = new DateTimeImmutable("now", timezone_open("Europe/Paris"));
        } elseif (is_string($datecreation)) {
            $this->datecreation = DateTimeImmutable::createFromFormat(
                DateTime::RFC3339,
                $datecreation,
                new DateTimeZone('Europe/Paris')
            );
        }
    }

    /**
     * @param DateTimeImmutable|string $datemodif
     */
    public function setdatemodif($datemodif): void
    {
        if ($datemodif instanceof DateTimeImmutable) {
            $this->datemodif = $datemodif;
        } elseif (is_string($datemodif)) {
            $this->datemodif = DateTimeImmutable::createFromFormat(
                DateTime::RFC3339,
                $datemodif,
                new DateTimeZone('Europe/Paris')
            );
        }
    }

    /**
     * @param DateTimeImmutable|string $daterender
     */
    public function setdaterender($daterender): void
    {
        if ($daterender instanceof DateTimeImmutable) {
            $this->daterender = $daterender;
        } elseif (is_string($daterender)) {
            $this->daterender = DateTimeImmutable::createFromFormat(
                DateTime::RFC3339,
                $daterender,
                new DateTimeZone('Europe/Paris')
            );
        }
    }


    public function setcss(string $css): void
    {
        if (strlen($css) < self::LENGTH_LONG_TEXT) {
            $this->css = trim($css);
        }
    }



    public function setjavascript(string $javascript): void
    {
        if (strlen($javascript) < self::LENGTH_LONG_TEXT) {
            $this->javascript = $javascript;
        }
    }


    public function setbody(string $body): void
    {
        if (strlen($body) < self::LENGTH_LONG_TEXT) {
            $body = crlf2lf($body);
            $this->body = $body;
        }
    }

    /**
     * @param string[] $externalcss
     */
    public function setexternalcss(array $externalcss): void
    {
        $this->externalcss = array_values(array_filter($externalcss));
    }

    public function setcustomhead(string $customhead): void
    {
        $this->customhead = crlf2lf($customhead);
    }

    public function setsecure(int $secure): void
    {
        if ($secure >= 0 and $secure <= self::SECUREMAX) {
            $this->secure = $secure;
        }
    }

    public function setinterface(string $interface): void
    {
        if (in_array($interface, $this::TABS)) {
            $this->interface = $interface;
        }
    }

    /**
     * @param string[]|string|null $linkto
     */
    public function setlinkto($linkto): void
    {
        if (is_array($linkto)) {
            $this->linkto = $linkto;
        } elseif (is_string($linkto)) {
            $linktojson = json_decode($linkto);
            if (is_array($linktojson)) {
                $this->linkto = $linktojson;
            }
        } elseif ($linkto === null) {
            $this->linkto = [];
        }
    }

    public function settemplatebody(string $templatebody): void
    {
        $this->templatebody = $templatebody;
    }

    public function settemplatecss(string $templatecss): void
    {
        $this->templatecss = $templatecss;
    }

    public function settemplatejavascript(string $templatejavascript): void
    {
        $this->templatejavascript = $templatejavascript;
    }

    public function setfavicon(string $favicon): void
    {
        $this->favicon = $favicon;
    }

    public function setthumbnail(string $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * @param string[] $authors
     */
    public function setauthors(array $authors): void
    {
        $this->authors = array_unique(array_values(array_filter($authors)));
    }

    /**
     * @deprecated 2.4.0 Replaced by displaycount
     */
    public function setaffcount(int $affcount): void
    {
        $this->setdisplaycount($affcount);
    }

    public function setdisplaycount(int $displaycount): void
    {
        $this->displaycount = $displaycount;
    }

    public function setvisitcount(int $visitcount): void
    {
        $this->visitcount = $visitcount;
    }

    public function seteditcount(int $editcount): void
    {
        $this->editcount = $editcount;
    }

    public function setsleep(int $sleep): void
    {
        if ($sleep > 180) {
            $sleep = 180;
        }
        $this->sleep = $sleep;
    }

    public function setredirection(string $redirection): void
    {
        if (strlen($redirection) <= 64) {
            $redirection = strip_tags($redirection);
            if (preg_match('%https?:\/\/\S*%', $redirection, $out)) {
                $this->redirection = $out[0];
            } else {
                $redirection = Model::idclean($redirection);
                if ($redirection !== $this->id) {
                    $this->redirection = $redirection;
                }
            }
        }
    }

    public function setrefresh(int $refresh): void
    {
        if ($refresh > 180) {
            $refresh = 180;
        } elseif ($refresh < 0) {
            $refresh = 0;
        }
        $this->refresh = $refresh;
    }

    public function setpassword(?string $password): void
    {
        if ($password === null) { // in order to fix Pages that stored password as null
            $password = '';
        }
        if (strlen($password) < 64) {
            $this->password = $password;
        }
    }

    public function setpostprocessaction(bool $postprocessaction): void
    {
        $this->postprocessaction = $postprocessaction;
    }

    /**
     * @param array<string, ?bool> $externallinks
     */
    public function setexternallinks(array $externallinks): void
    {
        $this->externallinks = $externallinks;
    }


    // __________________________________ C O U N T E R S ______________________________


    public function addeditcount(): void
    {
        $this->editcount++;
    }

    public function adddisplaycount(): void
    {
        $this->displaycount++;
    }

    public function addvisitcount(): void
    {
        $this->visitcount++;
    }

    /**
     * Update last edited date
     */
    public function updateedited(): void
    {
        $now = new DateTimeImmutable("now", timezone_open("Europe/Paris"));
        $this->setdatemodif($now);
        $this->addeditcount();
    }

    public function addauthor(string $id): void
    {
        if (!in_array($id, $this->authors)) {
            $this->authors[] = $id;
        }
    }

    /**
     * Merge new tag with actual tags
     *
     * @param string|string[] $tag Could be tags as string or array
     */

    public function addtag($tag): void
    {
        if (is_string($tag)) {
                $tag = self::tagtoarray($tag);
        }
        if (is_array($tag)) {
            $tag = array_map(function ($id) {
                return Model::idclean($id);
            }, $tag);
            $tag = array_filter($tag);
            $this->tag = array_unique(array_merge($this->tag, $tag));
        }
    }

    public function deadlinkcount(): int
    {
        $deadurls = array_filter($this->externallinks, function ($status): bool {
            return $status === false;
        });
        return count($deadurls);
    }

    /**
     * Used in the title of external links column in home view
     *
     * @return string                       All links separated by new lines followed by a emoji ✅ or 💀
     */
    public function externallinkstitle(): string
    {
        if (Config::urlchecker()) {
            $links = $this->externallinks;
            array_walk($links, function (&$value, string $key) {
                if (is_null($value)) {
                    $symbol = '🔍️';
                } else {
                    $symbol = $value ? '✅' : '💀';
                }
                $value = $key . ' ' . $symbol;
            });
        } else {
            $links = array_keys($this->externallinks);
        }
        return implode("\n", $links);
    }

    public function uncheckedlinkcount(): int
    {
        $uncheckedurls = array_filter($this->externallinks, function ($status): bool {
            return is_null($status);
        });
        return count($uncheckedurls);
    }


    // _________________________________ T O O L S ______________________________________

    /**
     * Convert a tag string to an array ready to be stored
     *
     * @param string $tagstring Tag as string separated by commas
     * @return string[] Tags stored as an array
     */

    public static function tagtoarray(string $tagstring): array
    {
        $tag = strip_tags(trim(strtolower($tagstring)));
        $tag = str_replace(' ', '', $tag);
        $taglist = explode(",", $tag);
        $taglist = array_filter($taglist);
        return $taglist;
    }
}
