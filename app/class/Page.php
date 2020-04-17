<?php

namespace Wcms;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;

class Page extends Dbitem
{
    protected $id;
    protected $title;
    protected $description;
    protected $tag;
    protected $date;
    protected $datecreation;
    protected $datemodif;
    protected $daterender;
    protected $css;
    protected $javascript;
    protected $body;
    protected $header;
    protected $main;
    protected $nav;
    protected $aside;
    protected $footer;
    protected $externalcss;
    protected $customhead;
    protected $secure;
    protected $interface;
    protected $linkto;
    protected $templatebody;
    protected $templatecss;
    protected $templatejavascript;
    protected $templateoptions;
    protected $favicon;
    protected $thumbnail;
    protected $authors;
    protected $invites;
    protected $readers;
    protected $affcount;
    protected $visitcount;
    protected $editcount;
    protected $editby;
    protected $sleep;
    protected $redirection;
    protected $refresh;


    public const LEN = 255;
    public const LENTEXT = 2 ** 20;
    public const SECUREMAX = 2;
    public const TABS = ['main', 'css', 'header', 'body', 'nav', 'aside', 'footer', 'javascript'];
    public const VAR_DATE = ['date', 'datecreation', 'datemodif', 'daterender'];

      
      

// _____________________________________________________ F U N ____________________________________________________

    public function __construct($datas = [])
    {
        $this->reset();
        $this->hydrate($datas);
    }

    public function getobjectvars(): array
    {
        return get_object_vars($this);
    }

    public function reset()
    {
        $now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));

        $this->settitle($this->id());
        $this->setdescription('');
        $this->settag([]);
        $this->setdate($now);
        $this->setdatecreation($now);
        $this->setdatecreation($now);
        $this->setdatemodif($now);
        $this->setdaterender($now);
        $this->setcss('');
        $this->setjavascript('');
        $this->setbody(Config::defaultbody());
        $this->setheader('');
        $this->setmain('');
        $this->setnav('');
        $this->setaside('');
        $this->setfooter('');
        $this->setexternalcss([]);
        $this->setcustomhead('');
        $this->setsecure(Config::defaultprivacy());
        $this->setinterface('main');
        $this->setlinkto([]);
        $this->settemplatebody('');
        $this->settemplatecss('');
        $this->settemplatejavascript('');
        $this->settemplateoptions(['externalcss', 'externaljavascript', 'favicon', 'thumbnail', 'reccursivecss']);
        $this->setfavicon('');
        $this->setthumbnail('');
        $this->setauthors([]);
        $this->setinvites([]);
        $this->setreaders([]);
        $this->setaffcount(0);
        $this->setvisitcount(0);
        $this->seteditcount(0);
        $this->seteditby([]);
        $this->setsleep(0);
        $this->setredirection('');
        $this->setrefresh(0);
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
                return substr($this->description, 0, 20) . '...';
        } else {
            return $this->description;
        }
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

    public function header($type = 'string')
    {
        return $this->header;
    }

    public function main($type = 'string')
    {
        return $this->main;
    }

    public function nav($type = "string")
    {
        return $this->nav;
    }

    public function aside($type = "string")
    {
        return $this->aside;
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
            return substr_count($this->customhead, PHP_EOL) + 1;
        }
    }

    public function footer($type = "string")
    {
        return $this->footer;
    }

    public function secure($type = 'int')
    {
        if ($type == 'string') {
            if ($this->secure == 0) {
                $secure = 'public';
            }
            if ($this->secure == 1) {
                $secure = 'private';
            }
            if ($this->secure == 2) {
                $secure = 'not_published';
            }
            return $secure;
        } else {
            return $this->secure;
        }
    }

    public function invitepassword($type = 'string')
    {
        return $this->invitepassword;
    }

    public function readpassword($type = 'string')
    {
        return $this->readpassword;
    }

    public function interface($type = 'string')
    {
        return $this->interface;
    }

    public function linkto($option = 'array')
    {
        if ($option == 'json') {
            $linkto = json_encode($this->linkto);
        } elseif ($option == 'array') {
            $linkto = $this->linkto;
        } elseif ($option == 'sort') {
            return count($this->linkto);
        } elseif ($option == 'string') {
            return implode(', ', $this->linkto);
        }
        return $linkto;
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

    public function template()
    {
        $template['body'] = $this->templatebody;
        $template['css'] = $this->templatecss;
        $template['javascript'] = $this->templatejavascript;

        $template['cssreccursive'] = $this->checkoption('reccursive');
        $template['externalcss'] = $this->checkoption('externalcss');
        $template['cssfavicon'] = $this->checkoption('favicon');
        $template['cssthumbnail'] = $this->checkoption('thumbnail');

        $template['externaljavascript'] = $this->checkoption('externaljavascript');

        return $template;
    }

    public function templateoptions($type = 'array')
    {
        return $this->templateoptions;
    }

    public function checkoption($option)
    {
        if (in_array($option, $this->templateoptions)) {
            return true;
        } else {
            return false;
        }
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

    public function invites($type = 'array')
    {
        return $this->invites;
    }

    public function readers($type = 'array')
    {
        return $this->invites;
    }

    public function affcount($type = 'int')
    {
        return $this->affcount;
    }

    public function visitcount($type = 'int')
    {
        return $this->visitcount;
    }

    public function editcount($type = 'int')
    {
        return $this->editcount;
    }

    public function editby($type = 'array')
    {
        return $this->editby;
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




    // _____________________________________________________ S E T ____________________________________________________

    public function setid($id)
    {
        if (strlen($id) <= Model::MAX_ID_LENGTH and is_string($id)) {
            $this->id = strip_tags(strtolower(str_replace(" ", "", $id)));
        }
    }

    public function settitle($title)
    {
        if (strlen($title) < self::LEN and is_string($title)) {
            $this->title = strip_tags(trim($title));
        }
    }

    public function setdescription($description)
    {
        if (strlen($description) < self::LEN and is_string($description)) {
            $this->description = strip_tags(trim($description));
        }
    }

    public function settag($tag)
    {
        if (is_string($tag) && strlen($tag) < self::LEN) {
                $tag = $this->tagtoarray($tag);
        }
        if (is_array($tag)) {
            $tag = array_map('idclean', $tag);
            $tag = array_filter($tag);
            $this->tag = $tag;
        }
    }

    public function setdate($date)
    {
        if ($date instanceof DateTimeImmutable) {
            $this->date = $date;
        } else {
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
            $this->datecreation = new DateTimeImmutable(null, timezone_open("Europe/Paris"));
        } else {
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
        } else {
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
        } else {
            $this->daterender = DateTimeImmutable::createFromFormat(
                DateTime::ISO8601,
                $daterender,
                new DateTimeZone('Europe/Paris')
            );
        }
    }


    public function setcss($css)
    {
        if (strlen($css) < self::LENTEXT and is_string($css)) {
            $this->css = trim(strtolower($css));
        }
    }



    public function setjavascript($javascript)
    {
        if (strlen($javascript < self::LENTEXT && is_string($javascript))) {
            $this->javascript = $javascript;
        }
    }


    public function setbody($body)
    {
        if (strlen($body < self::LENTEXT && is_string($body))) {
            $this->body = $body;
        }
    }

    public function setheader($header)
    {
        if (strlen($header < self::LENTEXT && is_string($header))) {
            $this->header = $header;
        }
    }

    public function setmain($main)
    {
        if (strlen($main) < self::LENTEXT and is_string($main)) {
            $this->main = $main;
        }
    }

    public function setnav($nav)
    {
        if (strlen($nav) < self::LENTEXT and is_string($nav)) {
            $this->nav = $nav;
        }
    }

    public function setaside($aside)
    {
        if (strlen($aside) < self::LENTEXT and is_string($aside)) {
            $this->aside = $aside;
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
            $this->customhead = $customhead;
        }
    }

    public function setfooter($footer)
    {
        if (strlen($footer) < self::LENTEXT and is_string($footer)) {
            $this->footer = $footer;
        }
    }

    public function setsecure($secure)
    {
        if ($secure >= 0 and $secure <= self::SECUREMAX) {
            $this->secure = intval($secure);
        }
    }

    public function setinvitepassword($invitepassword)
    {
        if (is_string($invitepassword) && strlen($invitepassword) < self::LEN) {
            $this->invitepassword = $invitepassword;
        }
    }

    public function setreadpassword($readpassword)
    {
        if (is_string($readpassword) && strlen($readpassword) < self::LEN) {
            $this->readpassword = $readpassword;
        }
    }

    public function setinterface($interface)
    {
        if (in_array($interface, self::TABS)) {
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

    public function settemplateoptions($templateoptions)
    {
        if (is_array($templateoptions)) {
            $this->templateoptions = array_values(array_filter($templateoptions));
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

    public function setinvites($invites)
    {
        if (is_array($invites)) {
            $this->invites = array_values(array_filter($invites));
        }
    }

    public function setreaders($readers)
    {
        if (is_array($readers)) {
            $this->readers = array_values(array_filter($readers));
        }
    }

    public function setaffcount($affcount)
    {
        if (is_int($affcount)) {
            $this->affcount = $affcount;
        } elseif (is_numeric($affcount)) {
            $this->affcount = intval($affcount);
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

    public function seteditby($editby)
    {
        if (is_array($editby)) {
            $this->editby = $editby;
        }
    }

    public function setredirection($redirection)
    {
        if (is_string($redirection) && strlen($redirection) <= 64) {
            $redirection = strip_tags($redirection);
            if (preg_match('%https?:\/\/\S*%', $redirection, $out)) {
                $this->redirection = $out[0];
            } else {
                $redirection = idclean($redirection);
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


    // __________________________________ C O U N T E R S ______________________________


    public function addeditcount()
    {
        $this->editcount++;
    }

    public function addaffcount()
    {
        $this->affcount++;
    }

    public function addvisitcount()
    {
        $this->visitcount++;
    }

    public function updateedited()
    {
        $now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));
        $this->setdatemodif($now);
        $this->addeditcount();
    }

    public function addauthor(string $id)
    {
        if (!in_array($id, $this->authors)) {
            $this->authors[] = $id;
        }
    }

    public function addeditby(string $id)
    {
        $this->editby[$id] = true;
    }

    public function removeeditby(string $id)
    {
        unset($this->editby[$id]);
    }

    public function iseditedby()
    {
        return count($this->editby) > 0;
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
            $tag = array_map('idclean', $tag);
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
