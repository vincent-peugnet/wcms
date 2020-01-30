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
	protected $linkfrom;
	protected $linkto;
	protected $templatebody;
	protected $templatecss;
	protected $templatejavascript;
	protected $templateoptions;
	protected $favicon;
	protected $authors;
	protected $invites;
	protected $readers;
	protected $affcount;
	protected $visitcount;
	protected $editcount;
	protected $editby;
	protected $sleep;


	const LEN = 255;
	const LENTEXT = 2**20;
	const SECUREMAX = 2;
	const TABS = ['main', 'css', 'header', 'body', 'nav', 'aside', 'footer', 'javascript'];
	const VAR_DATE = ['date', 'datecreation', 'datemodif', 'daterender'];

	  
	  

// _____________________________________________________ F U N ____________________________________________________

	public function __construct($datas = [])
	{
		$this->reset();
		$this->hydrate($datas);
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
		$this->setbody('');
		$this->setheader('');
		$this->setmain('');
		$this->setnav('');
		$this->setaside('');
		$this->setfooter('');
		$this->setexternalcss([]);
		$this->setcustomhead('');
		$this->setsecure(Config::defaultprivacy());
		$this->setinterface('main');
		$this->setlinkfrom([]);
		$this->setlinkto([]);
		$this->settemplatebody('');
		$this->settemplatecss('');
		$this->settemplatejavascript('');
		$this->settemplateoptions(['externalcss', 'externaljavascript', 'favicon', 'thumbnail', 'reccursivecss']);
		$this->setfavicon('');
		$this->setauthors([]);
		$this->setinvites([]);
		$this->setreaders([]);
		$this->setaffcount(0);
		$this->setvisitcount(0);
		$this->seteditcount(0);
		$this->seteditby([]);
		$this->setsleep(0);
	}

	public static function classvarlist()
	{
		$classvarlist = [];
		foreach (get_class_vars(__class__) as $var => $default) {
			$classvarlist[] = $var;
		}
		return ['pagevarlist' => $classvarlist];
	}

	/**
	 * Check if page have a thumbnail
	 * @return bool true if the page have a thumbnail otherwise return false.
	 */
	public function thumbnailexist() : bool
	{
		$thumbnaillink = Model::THUMBNAIL_DIR . $this->id . '.jpg';

		$test = file_exists($thumbnaillink);

		$exist =  file_exists(Model::THUMBNAIL_DIR . $this->id . '.jpg');

		return $exist;
	}


		// _____________________________________________________ G E T ____________________________________________________

	public function id($type = 'string')
	{
		return $this->id;
	}

	public function title($type = 'string')
	{
		if($type == 'sort') {
			return strtolower($this->title);
		} else {
			return $this->title;
		}
	}

	public function description($type = 'string')
	{
		if($type == 'short' && strlen($this->description) > 15 ) {
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
		if ($option == 'string') {
			return $this->date->format(DateTime::ISO8601);
		} elseif ($option == 'date' || $option == 'sort') {
			return $this->date;
		} elseif ($option == 'hrdi') {
			$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));
			return hrdi($this->date->diff($now));
		} elseif ($option == 'pdate') {
			return $this->date->format('Y-m-d');
		} elseif ($option == 'ptime') {
			return $this->date->format('H:i');
		} elseif ($option = 'dmy') {
			return $this->date->format('d/m/Y');
		}


	}

	public function datecreation($option = 'date')
	{
		if ($option == 'string') {
			return $this->datecreation->format(DateTime::ISO8601);
		} elseif ($option == 'date' || $option == 'sort') {
			return $this->datecreation;
		} elseif ($option == 'hrdi') {
			$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));
			return hrdi($this->datecreation->diff($now));
		}
	}


	public function datemodif($option = 'date')
	{
		if ($option == 'string') {
			return $this->datemodif->format(DateTime::ISO8601);
		} elseif ($option == 'date' || $option == 'sort') {
			return $this->datemodif;
		} elseif ($option == 'hrdi') {
			$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));
			return hrdi($this->datemodif->diff($now));
		}
	}

	public function daterender($option = 'date')
	{
		if ($option == 'string') {
			return $this->daterender->format(DateTime::ISO8601);
		} elseif ($option == 'date' || $option == 'sort') {
			return $this->daterender;
		} elseif ($option == 'hrdi') {
			$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));
			return hrdi($this->daterender->diff($now));
		}
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
		if($type === 'string') {
			return $this->customhead;
		} elseif($type === 'int') {
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
			if ($this->secure == 0) $secure = 'public';
			if ($this->secure == 1) $secure = 'private';
			if ($this->secure == 2) $secure = 'not_published';
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

	public function linkfrom($option = 'array')
	{
		if ($option == 'json') {
			$linkfrom = json_encode($this->linkfrom);
		} elseif ($option == 'array') {
			$linkfrom = $this->linkfrom;
		} elseif ($option == 'sort') {
			return count($this->linkfrom);
		} elseif ($option == 'string') {
			return implode(', ', $this->linkfrom);
		}
		return $linkfrom;

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

	function checkoption($option)
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

	public function authors($type = 'array')
	{
		if($type == 'string') {
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
			$this->tag = $tag;
		}
	}

	public function setdate($date)
	{
		if ($date instanceof DateTimeImmutable) {
			$this->date = $date;
		} else {
			$this->date = DateTimeImmutable::createFromFormat(DateTime::ISO8601, $date, new DateTimeZone('Europe/Paris'));
		}
	}

	public function setptime($ptime)
	{
		if(is_string($ptime) && DateTime::createFromFormat('H:i', $ptime) !== FALSE) {
			$time = explode(':', $ptime);
			$this->date = $this->date->setTime($time[0], $time[1]);
		}
	}

	public function setpdate($pdate)
	{
		if(is_string($pdate) &&  DateTime::createFromFormat('Y-m-d', $pdate) !== FALSE) {
			$date = explode('-', $pdate);
			$this->date = $this->date->setDate($date[0], $date[1], $date[2]);
		}
	}

	public function setdatecreation($datecreation)
	{
		if ($datecreation instanceof DateTimeImmutable) {
			$this->datecreation = $datecreation;
		} else {
			$this->datecreation = DateTimeImmutable::createFromFormat(DateTime::ISO8601, $datecreation, new DateTimeZone('Europe/Paris'));
		}
	}

	public function setdatemodif($datemodif)
	{
		if ($datemodif instanceof DateTimeImmutable) {
			$this->datemodif = $datemodif;
		} else {
			$this->datemodif = DateTimeImmutable::createFromFormat(DateTime::ISO8601, $datemodif, new DateTimeZone('Europe/Paris'));
		}
	}

	public function setdaterender($daterender)
	{
		if ($daterender instanceof DateTimeImmutable) {
			$this->daterender = $daterender;
		} else {
			$this->daterender = DateTimeImmutable::createFromFormat(DateTime::ISO8601, $daterender, new DateTimeZone('Europe/Paris'));
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
		if(is_array($externalcss)) {
			$this->externalcss = array_values(array_filter($externalcss));
		}
	}

	public function setcustomhead(string $customhead)
	{
		if(is_string($customhead)) {
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

	public function setlinkfrom($linkfrom)
	{
		if (is_array($linkfrom)) {
			$this->linkfrom = $linkfrom;
		} elseif (is_string($linkfrom)) {
			$linkfromjson = json_decode($linkfrom);
			if (is_array($linkfromjson)) {
				$this->linkfrom = $linkfromjson;
			}
		} elseif ($linkfrom === null) {
			$this->linkfrom = [];
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
		if(is_array($templateoptions)) {
			$this->templateoptions = array_values(array_filter($templateoptions));
		}
	}

	public function setfavicon($favicon)
	{
		if (is_string($favicon)) {
			$this->favicon = $favicon;
		}
	}

	public function setauthors($authors)
	{
		if(is_array($authors)) {
			$this->authors = array_unique(array_values(array_filter($authors)));
		}
	}

	public function setinvites($invites)
	{
		if(is_array($invites)) {
			$this->invites = array_values(array_filter($invites));
		}
	}

	public function setreaders($readers)
	{
		if(is_array($readers)) {
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
		if(is_array($editby)) {
			$this->editby = $editby;
		}
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
		if(!in_array($id, $this->authors)) {
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
		if($sleep > 180) {
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
		if(is_array($tag)) {
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

	private function tagtoarray(string $tagstring) : array
	{
		$tag = strip_tags(trim(strtolower($tagstring)));
		$tag = str_replace('*', '', $tag);
		$tag = str_replace(' ', '', $tag);
		$taglist = explode(",", $tag);
		$taglist = array_filter($taglist);
		return $taglist;
	}

}


?>