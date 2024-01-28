<?php

namespace Wcms;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;

abstract class Page extends Item
{
    protected $id;
    protected $title;
    protected $description;
    protected $lang;
    protected $tag;
    protected ?float $latitude;
    protected ?float $longitude;
    protected $date;
    protected $datecreation;
    protected $datemodif;
    protected $daterender;
    protected $css;
    protected $javascript;
    protected $body;
    protected $externalcss;
    protected $customhead;
    protected $secure;
    protected $interface;
    protected $linkto;
    protected $templatebody;
    protected $templatecss;
    protected $templatejavascript;
    protected $favicon;
    protected $thumbnail;
    protected $authors;
    protected $displaycount;
    protected $visitcount;
    protected $editcount;
    protected $sleep;
    protected $redirection;
    protected $refresh;
    protected $password;
    protected $postprocessaction;

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

    public function reset()
    {
        $now = new DateTimeImmutable("now", timezone_open("Europe/Paris"));

        $this->settitle($this->id());
        $this->setdescription('');
        $this->setlang('');
        $this->settag([]);
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
        $this->settemplatebody('');
        $this->settemplatecss('');
        $this->settemplatejavascript('');
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
     * Indicate if Page is geolocated. Meaning it's latitude and longitude are both set (they are not null)
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

    public function id($type = 'string')
    {
        return $this->id;
    }

    public function title($type = 'string')
    {
        if ($type == 'sort') {
            return strtolower($this->title);
        } else {
            return $this->title;
        }
    }

    public function description($type = 'string')
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

    public function tag($option = 'array')
    {
        if ($option == 'string') {
            return implode(", ", $this->tag);
        } elseif ($option == 'array') {
            return $this->tag;
        } elseif ($option == 'sort') {
            return count($this->tag);
        }
    }

    public function latitude($option = 'float'): ?float
    {
        return $this->latitude;
    }

    public function longitude($optiob = 'float'): ?float
    {
        return $this->longitude;
    }

    public function date($option = 'date')
    {
        return $this->datetransform('date', $option);
    }

    public function datecreation($option = 'date')
    {
        return $this->datetransform('datecreation', $option);
    }


    public function datemodif($option = 'date')
    {
        return $this->datetransform('datemodif', $option);
    }

    public function daterender($option = 'date')
    {
        return $this->datetransform('daterender', $option);
    }

    public function primary($type = ''): string
    {
        return '';
    }

    public function css($type = 'string')
    {
        return $this->css;
    }

    public function javascript($type = 'string')
    {
        return $this->javascript;
    }

    public function body($type = 'string')
    {
        return $this->body;
    }
    public function externalcss($type = "array")
    {
        return $this->externalcss;
    }

    public function customhead($type = "string")
    {
        if ($type === 'string') {
            return $this->customhead;
        } elseif ($type === 'int') {
            return substr_count($this->customhead, "\n") + 1;
        }
    }

    public function secure($type = 'int')
    {
        if ($type == 'string') {
            return Modelpage::SECURE_LEVELS[$this->secure];
        } else {
            return $this->secure;
        }
    }

    public function interface($type = 'string')
    {
        return $this->interface;
    }

    public function linkto($option = 'array')
    {
        if ($option == 'json') {
            return json_encode($this->linkto);
        } elseif ($option == 'array') {
            return $this->linkto;
        } elseif ($option == 'sort') {
            return count($this->linkto);
        } elseif ($option == 'string') {
            return implode(', ', $this->linkto);
        }
        return $this->linkto;
    }

    public function templatebody($type = 'string')
    {
        return $this->templatebody;
    }

    public function templatecss($type = 'string')
    {
        return $this->templatecss;
    }

    public function templatejavascript($type = 'string')
    {
        return $this->templatejavascript;
    }

    public function favicon($type = 'string')
    {
        return $this->favicon;
    }

    public function thumbnail($type = 'string')
    {
        return $this->thumbnail;
    }

    public function authors($type = 'array')
    {
        if ($type == 'string') {
            return implode(', ', $this->authors);
        } elseif ($type == 'array') {
            return $this->authors;
        } elseif ($type == 'sort') {
            return count($this->authors);
        }
    }

    public function displaycount($type = 'int'): int
    {
        return $this->displaycount;
    }

    public function visitcount($type = 'int'): int
    {
        return $this->visitcount;
    }

    public function editcount($type = 'int'): int
    {
        return $this->editcount;
    }

    public function sleep($type = 'int')
    {
        return $this->sleep;
    }

    public function redirection($type = 'string')
    {
        return $this->redirection;
    }

    public function refresh($type = 'int')
    {
        return $this->refresh;
    }

    public function password($type = 'string')
    {
        return $this->password;
    }

    public function postprocessaction($type = 'int'): bool
    {
        return $this->postprocessaction;
    }

    public function version($type = 'int'): int
    {
        return $this->version;
    }




    // _____________________________________________________ S E T ____________________________________________________

    public function setid($id)
    {
        if (is_string($id) && strlen($id) <= Model::MAX_ID_LENGTH) {
            $this->id = strip_tags(strtolower(str_replace(" ", "", $id)));
        }
    }

    public function settitle($title)
    {
        if (strlen($title) < self::LENGTH_SHORT_TEXT and is_string($title)) {
            $this->title = strip_tags(trim($title));
        }
    }

    public function setdescription($description)
    {
        if (strlen($description) < self::LENGTH_SHORT_TEXT and is_string($description)) {
            $this->description = strip_tags(trim($description));
        }
    }

    public function setlang(string $lang)
    {
        $this->lang = mb_substr(strip_tags($lang), 0, Config::LANG_MAX);
    }

    public function settag($tag)
    {
        if (is_string($tag) && strlen($tag) < self::LENGTH_SHORT_TEXT) {
                $tag = $this->tagtoarray($tag);
        }
        if (is_array($tag)) {
            $tag = array_map(function ($id) {
                return Model::idclean($id);
            }, $tag);
            $tag = array_filter($tag);
            $this->tag = $tag;
        }
    }

    /**
     * @param ?float $latitude              Must be `null` or a float between -90 and 90 degres
     */
    public function setlatitude($latitude): void
    {
        if (is_numeric($latitude)) {
            $latitude = floatval($latitude);
            if ($latitude >= -90 && $latitude <= 90) {
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
            if ($longitude >= -90 && $longitude <= 90) {
                $this->longitude = $longitude;
            }
        } else {
            $this->longitude = null;
        }
    }

    public function setdate($date)
    {
        if ($date instanceof DateTimeImmutable) {
            $this->date = $date;
        } elseif (is_string($date)) {
            $this->date = DateTimeImmutable::createFromFormat(
                DateTime::ISO8601,
                $date,
                new DateTimeZone('Europe/Paris')
            );
        }
    }

    public function setptime($ptime)
    {
        if (is_string($ptime) && DateTime::createFromFormat('H:i', $ptime) !== false) {
            $time = explode(':', $ptime);
            $this->date = $this->date->setTime($time[0], $time[1]);
        }
    }

    public function setpdate($pdate)
    {
        if (is_string($pdate) &&  DateTime::createFromFormat('Y-m-d', $pdate) !== false) {
            $date = explode('-', $pdate);
            $this->date = $this->date->setDate($date[0], $date[1], $date[2]);
        }
    }

    /**
     * DateTimeImmutable : set date
     * string ISO8601 : set date
     * true : reset to now
     *
     * @param string|DateTimeImmutable|true $datecreation Set or reset date of creation
     */
    public function setdatecreation($datecreation)
    {
        if ($datecreation instanceof DateTimeImmutable) {
            $this->datecreation = $datecreation;
        } elseif ($datecreation === true) {
            $this->datecreation = new DateTimeImmutable("now", timezone_open("Europe/Paris"));
        } elseif (is_string($datecreation)) {
            $this->datecreation = DateTimeImmutable::createFromFormat(
                DateTime::ISO8601,
                $datecreation,
                new DateTimeZone('Europe/Paris')
            );
        }
    }

    public function setdatemodif($datemodif)
    {
        if ($datemodif instanceof DateTimeImmutable) {
            $this->datemodif = $datemodif;
        } elseif (is_string($datemodif)) {
            $this->datemodif = DateTimeImmutable::createFromFormat(
                DateTime::ISO8601,
                $datemodif,
                new DateTimeZone('Europe/Paris')
            );
        }
    }

    public function setdaterender($daterender)
    {
        if ($daterender instanceof DateTimeImmutable) {
            $this->daterender = $daterender;
        } elseif (is_string($daterender)) {
            $this->daterender = DateTimeImmutable::createFromFormat(
                DateTime::ISO8601,
                $daterender,
                new DateTimeZone('Europe/Paris')
            );
        }
    }


    public function setcss($css)
    {
        if (strlen($css) < self::LENGTH_LONG_TEXT and is_string($css)) {
            $this->css = trim($css);
        }
    }



    public function setjavascript($javascript)
    {
        if (strlen($javascript) < self::LENGTH_LONG_TEXT && is_string($javascript)) {
            $this->javascript = $javascript;
        }
    }


    public function setbody($body)
    {
        if (strlen($body) < self::LENGTH_LONG_TEXT && is_string($body)) {
            $body = crlf2lf($body);
            $this->body = $body;
        }
    }

    public function setexternalcss($externalcss)
    {
        if (is_array($externalcss)) {
            $this->externalcss = array_values(array_filter($externalcss));
        }
    }

    public function setcustomhead(string $customhead)
    {
        if (is_string($customhead)) {
            $customhead = crlf2lf($customhead);
            $this->customhead = $customhead;
        }
    }

    public function setsecure($secure)
    {
        if ($secure >= 0 and $secure <= self::SECUREMAX) {
            $this->secure = intval($secure);
        }
    }

    public function setinterface($interface)
    {
        if (in_array($interface, $this::TABS)) {
            $this->interface = $interface;
        }
    }

    public function setlinkto($linkto)
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

    public function settemplatebody($templatebody)
    {
        if (is_string($templatebody)) {
            $this->templatebody = $templatebody;
        }
    }

    public function settemplatecss($templatecss)
    {
        if (is_string($templatecss)) {
            $this->templatecss = $templatecss;
        }
    }

    public function settemplatejavascript($templatejavascript)
    {
        if (is_string($templatejavascript)) {
            $this->templatejavascript = $templatejavascript;
        }
    }

    public function setfavicon($favicon)
    {
        if (is_string($favicon)) {
            $this->favicon = $favicon;
        }
    }

    public function setthumbnail($thumbnail)
    {
        if (is_string($thumbnail)) {
            $this->thumbnail = $thumbnail;
        }
    }

    public function setauthors($authors)
    {
        if (is_array($authors)) {
            $this->authors = array_unique(array_values(array_filter($authors)));
        }
    }

    /**
     * @deprecated 2.4.0 Replaced by displaycount
     */
    public function setaffcount($affcount)
    {
        $this->setdisplaycount($affcount);
    }

    public function setdisplaycount($displaycount)
    {
        if (is_int($displaycount)) {
            $this->displaycount = $displaycount;
        } elseif (is_numeric($displaycount)) {
            $this->displaycount = intval($displaycount);
        }
    }

    public function setvisitcount($visitcount)
    {
        if (is_int($visitcount)) {
            $this->visitcount = $visitcount;
        } elseif (is_numeric($visitcount)) {
            $this->visitcount = intval($visitcount);
        }
    }

    public function seteditcount($editcount)
    {
        if (is_int($editcount)) {
            $this->editcount = $editcount;
        } elseif (is_numeric($editcount)) {
            $this->editcount = intval($editcount);
        }
    }

    public function setredirection($redirection)
    {
        if (is_string($redirection) && strlen($redirection) <= 64) {
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

    public function setrefresh($refresh)
    {
        $refresh = intval($refresh);
        if ($refresh > 180) {
            $refresh = 180;
        } elseif ($refresh < 0) {
            $refresh = 0;
        }
        $this->refresh = $refresh;
    }

    public function setpassword($password)
    {
        if (is_string($password) && strlen($password) < 64) {
            $this->password = $password;
        }
    }

    public function setpostprocessaction($postprocessaction): void
    {
        $this->postprocessaction = boolval($postprocessaction);
    }


    // __________________________________ C O U N T E R S ______________________________


    public function addeditcount()
    {
        $this->editcount++;
    }

    public function adddisplaycount()
    {
        $this->displaycount++;
    }

    public function addvisitcount()
    {
        $this->visitcount++;
    }

    /**
     * Update last edited date
     */
    public function updateedited()
    {
        $now = new DateTimeImmutable("now", timezone_open("Europe/Paris"));
        $this->setdatemodif($now);
        $this->addeditcount();
    }

    public function addauthor(string $id)
    {
        if (!in_array($id, $this->authors)) {
            $this->authors[] = $id;
        }
    }

    public function setsleep($sleep)
    {
        $sleep = abs(intval($sleep));
        if ($sleep > 180) {
            $sleep = 180;
        }
        $this->sleep = $sleep;
    }

    /**
     * Merge new tag with actual tags
     *
     * @param string|array $tag Could be tags as string or array
     */

    public function addtag($tag)
    {
        if (is_string($tag)) {
                $tag = $this->tagtoarray($tag);
        }
        if (is_array($tag)) {
            $tag = array_map(function ($id) {
                return Model::idclean($id);
            }, $tag);
            $tag = array_filter($tag);
            $this->tag = array_unique(array_merge($this->tag, $tag));
        }
    }


    // _________________________________ T O O L S ______________________________________

    /**
     * Convert a tag string to an array ready to be stored
     *
     * @param string $tagstring Tag as string separated by commas
     * @return array Tags stored as an array
     */

    private function tagtoarray(string $tagstring): array
    {
        $tag = strip_tags(trim(strtolower($tagstring)));
        $tag = str_replace(' ', '', $tag);
        $taglist = explode(",", $tag);
        $taglist = array_filter($taglist);
        return $taglist;
    }
}
