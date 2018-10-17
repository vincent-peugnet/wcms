<?php

use Michelf\MarkdownExtra;


class Art
{
	private $id;
	private $title;
	private $description;
	private $tag;
	private $date;
	private $datecreation;
	private $datemodif;
	private $daterender;
	private $css;
	private $quickcss;
	private $javascript;
	private $html;
	private $header;
	private $section;
	private $nav;
	private $aside;
	private $footer;
	private $render;
	private $secure;
	private $invitepassword;
	private $interface;
	private $linkfrom;
	private $template;
	private $affcount;
	private $editcount;

	private $linkto;

	const LEN = 255;
	const LENHTML = 20000;
	const SECUREMAX = 2;
	const LENCOULEUR = 7;
	const DEBUT = '(?id=';
	const FIN = ')';

	  
	  

// _____________________________________________________ F U N ____________________________________________________

	public function __construct(array $donnees)
	{
		$this->hydrate($donnees);
	}

	public function hydrate(array $donnees)
	{
		foreach ($donnees as $key => $value) {
			$method = 'set' . $key;

			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
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
		$this->setquickcss([]);
		$this->setjavascript('');
		$this->sethtml('');
		$this->setheader('');
		$this->setsection('');
		$this->setnav('');
		$this->setaside('');
		$this->setfooter('');
		$this->setsecure(2);
		$this->setinterface('section');
		$this->setlinkfrom([]);
		$this->settemplate([]);
		$this->setaffcount(0);
		$this->seteditcount(0);
	}

	public function updatelien()
	{
		$this->linkfrom = [];
		$this->linkfrom = array_unique(search($this->md(true), self::DEBUT, self::FIN));

	}

	public static function classvarlist()
	{
		$classvarlist = [];
		foreach (get_class_vars(__class__) as $var => $default) {
			$classvarlist[] = $var;
		}
		return ['artvarlist' => $classvarlist];
	}




	public function calcliento($getlist)
	{
		$liento = [];
		foreach ($getlist as $lien) {
			if (in_array($this->id(), $lien->lien('array'))) {
				$liento[] = $lien->id();
			}
		}
		$this->setliento($liento);
	}


	public function autotaglist()
	{
		$pattern = "/%%(\w*)%%/";
		preg_match_all($pattern, $this->md(), $out);
		return $out[1];

	}

	public function autotaglistupdate($taglist)
	{
		foreach ($taglist as $tag => $artlist) {
			$replace = '<ul>';
			foreach ($artlist as $art) {
				$replace .= '<li><a href="?id=' . $art->id() . '" title="' . $art->intro() . '">' . $art->titre() . '</a></li>';
			}
			$replace .= '</ul>';
			$this->html = str_replace('%%' . $tag . '%%', $replace, $this->html);
		}
	}

	public function autotaglistcalc($taglist)
	{
		foreach ($taglist as $tag => $artlist) {
			foreach ($artlist as $art) {
				if (!in_array($art->id(), $this->lien('array')) && $art->id() != $this->id()) {
					$this->lien[] = $art->id();
				}
			}
		}
	}


		// _____________________________________________________ G E T ____________________________________________________

	public function id($type = 'string')
	{
		return $this->id;
	}

	public function title($type = 'string')
	{
		return $this->title;
	}

	public function description($type = 'string')
	{
		return $this->description;
	}

	public function tag($option)
	{
		if ($option == 'string') {
			return implode(", ", $this->tag);
		} elseif ($option == 'array') {
			return $this->tag;
		} elseif ($option == 'sort') {
			return count($this->tag);
		}
	}

	public function date($option)
	{
		if ($option == 'string') {
			return $this->date->format('Y-m-d H:i:s');
		} elseif ($option == 'date' || $option == 'sort') {
			return $this->date;
		} elseif ($option == 'hrdi') {
			$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));
			return hrdi($this->date->diff($now));
		}
	}

	public function datecreation($option)
	{
		if ($option == 'string') {
			return $this->datecreation->format('Y-m-d H:i:s');
		} elseif ($option == 'date' || $option == 'sort') {
			return $this->datecreation;
		} elseif ($option == 'hrdi') {
			$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));
			return hrdi($this->datecreation->diff($now));
		}
	}


	public function datemodif($option)
	{
		if ($option == 'string') {
			return $this->datemodif->format('Y-m-d H:i:s');
		} elseif ($option == 'date' || $option == 'sort') {
			return $this->datemodif;
		} elseif ($option == 'hrdi') {
			$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));
			return hrdi($this->datemodif->diff($now));
		}
	}

	public function daterender($option)
	{
		if ($option == 'string') {
			return $this->daterender->format('Y-m-d H:i:s');
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

	public function quickcss($option = 'json')
	{
		if ($option == 'json') {
			return json_encode($this->quickcss);
		} elseif ($option == 'array') {
			return $this->quickcss;
		} elseif ($option == 'string') {
			$string = '';
			foreach ($this->quickcss as $key => $css) {
				$string .= PHP_EOL . $key . ' {';
				foreach ($css as $param => $value) {
					if(is_int($value)) {
						$string .= PHP_EOL . '    ' . $param . ': ' . $value . 'px;';
					} else {
						$string .= PHP_EOL . '    ' . $param . ': ' . $value . ';';
					}
				}
				$string .= PHP_EOL . '}' . PHP_EOL;
			}
			return $string;
		}
	}

	public function cssprint()
	{
		return $cssprint;
	}

	public function csstemplate(App $app)
	{
		$data = [];
		$temp = '';
		if (!empty($this->template())) {
			if ($app->exist($this->template()) and !in_array($this->template(), $data)) {
				$template = $app->get($this->template());
				$temp = $temp . $template->css($app);
				$data[] = $template->id();

			}

		}
		$cssprint = str_replace('url(/', 'url(' . $app::MEDIA_DIR, $temp . $this->css);
		return $cssprint;
	}

	public function md($expand = false)
	{
		if ($expand == true) {
			$md = str_replace('](=', '](?id=', $this->html);
		} else {
			$md = $this->html;
		}
		return $md;
	}

	public function html(App $app)
	{

		// %%%% TITLE & DESCIPTION
		$html = str_replace('%TITLE%', $this->titre(), $this->html);
		$html = str_replace('%DESCRIPTION%', $this->intro(), $html);

		$parser = new MarkdownExtra;

		// id in headers
		$parser->header_id_func = function ($header) {
			return preg_replace('/[^\w]/', '', strtolower($header));
		};
		$html = $parser->transform($html);

		// replace = > ?id=
		$html = str_replace('href="=', 'href="?id=', $html);


		// infobulles tooltip
		foreach ($this->lien('array') as $id) {
			$title = "Cet article n'existe pas encore";
			foreach ($app->getlister(['id', 'intro']) as $item) {
				if ($item->id() == $id) {
					$title = $item->intro();
				}
			}
			$lien = 'href="?id=' . $id . '"';
			$titlelien = ' title="' . $title . '" ' . $lien;
			$html = str_replace($lien, $titlelien, $html);
		}

		if (!empty(strstr($html, '%SUMMARY%'))) {



			$html = str_replace('%SUMMARY%', sumparser($html), $html);
		}


		$html = str_replace('href="./media/', ' class="file" target="_blank" href="./media/', $html);
		$html = str_replace('href="http', ' class="external" target="_blank" href="http', $html);
		$html = str_replace('<img src="/', '<img src="./media/', $html);
		$html = str_replace('<iframe', '<div class="iframe"><div class="container"><iframe class="video" ', $html);
		$html = str_replace('</iframe>', '</iframe></div></div>', $html);
		return $html;


	}

	public function secure($type = 'int')
	{
		if ($type == 'string') {
			if ($this->secure == 0) $secure = 'public';
			if ($this->secure == 1) $secure = 'private';
			if ($this->secure == 2) $secure = 'not published';
			return $secure;
		} else {
			return $this->secure;
		}
	}

	public function couleurtext()
	{
		return $this->couleurtext;
	}

	public function couleurbkg()
	{
		return $this->couleurbkg;
	}

	public function couleurlien()
	{
		return $this->couleurlien;
	}

	public function couleurlienblank()
	{
		return $this->couleurlienblank;
	}

	public function lien($option)
	{
		if ($option == 'string') {
			$lien = implode(", ", $this->lien);
		} elseif ($option == 'array') {
			$lien = $this->lien;
		} elseif ($option == 'sort') {
			return count($this->lien);
		}
		return $lien;

	}

	public function liento($option)
	{
		if ($option == 'string') {
			$liento = implode(", ", $this->liento);
		} elseif ($option == 'array') {
			$liento = $this->liento;
		} elseif ($option == 'sort') {
			return count($this->liento);
		}
		return $liento;

	}

	public function template($type = 'string')
	{
		return $this->template;
	}





		// _____________________________________________________ S E T ____________________________________________________

	public function setid($id)
	{
		if (strlen($id) < self::LEN and is_string($id)) {
			$this->id = strip_tags(strtolower(str_replace(" ", "", $id)));
		}
	}

	public function settitre($titre)
	{
		if (strlen($titre) < self::LEN and is_string($titre)) {
			$this->titre = strip_tags(trim($titre));
		}
	}

	public function setsoustitre($soustitre)
	{
		if (strlen($soustitre) < self::LEN and is_string($soustitre)) {
			$this->soustitre = strip_tags(trim($soustitre));
		}
	}

	public function setintro($intro)
	{
		if (strlen($intro) < self::LEN and is_string($intro)) {
			$this->intro = strip_tags(trim($intro));
		}
	}

	public function settag($tag)
	{
		if (is_string($tag)) {

			if (strlen($tag) < self::LEN and is_string($tag)) {
				$tag = strip_tags(trim(strtolower($tag)));
				$tag = str_replace('*', '', $tag);
				$tag = str_replace(' ', '', $tag);

				$taglist = explode(",", $tag);
				$taglist = array_filter($taglist);
				$this->tag = $taglist;
			}
		} elseif (is_array($tag)) {
			$this->tag = $tag;
		}
	}

	public function setdate($date)
	{
		if ($date instanceof DateTimeImmutable) {
			$this->date = $date;
		} else {
			$this->date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date, new DateTimeZone('Europe/Paris'));
		}
	}

	public function setdatecreation($datecreation)
	{
		if ($datecreation instanceof DateTimeImmutable) {
			$this->datecreation = $datecreation;
		} else {
			$this->datecreation = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $datecreation, new DateTimeZone('Europe/Paris'));
		}
	}

	public function setdatemodif($datemodif)
	{
		if ($datemodif instanceof DateTimeImmutable) {
			$this->datemodif = $datemodif;
		} else {
			$this->datemodif = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $datemodif, new DateTimeZone('Europe/Paris'));
		}
	}

	public function setdaterender($daterender)
	{
		if ($daterender instanceof DateTimeImmutable) {
			$this->daterender = $daterender;
		} else {
			$this->daterender = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $daterender, new DateTimeZone('Europe/Paris'));
		}
	}

	public function setquickcss($quickcss)
	{
		
	}

	public function setcss($css)
	{
		if (strlen($css) < self::LENHTML and is_string($css)) {
			$this->css = strip_tags(trim(strtolower($css)));
		}
	}

	public function sethtml($html)
	{
		if (strlen($html) < self::LENHTML and is_string($html)) {
			$this->html = $html;
		}
	}

	public function setsecure($secure)
	{
		if ($secure >= 0 and $secure <= self::SECUREMAX) {
			$this->secure = intval($secure);
		}
	}

	public function setcouleurtext($couleurtext)
	{
		$couleurtext = strval($couleurtext);
		if (strlen($couleurtext) <= self::LENCOULEUR) {
			$this->couleurtext = strip_tags(trim($couleurtext));
		}
	}

	public function setcouleurbkg($couleurbkg)
	{
		$couleurbkg = strval($couleurbkg);
		if (strlen($couleurbkg) <= self::LENCOULEUR) {
			$this->couleurbkg = strip_tags(trim($couleurbkg));
		}
	}

	public function setcouleurlien($couleurlien)
	{
		$couleurlien = strval($couleurlien);
		if (strlen($couleurlien) <= self::LENCOULEUR) {
			$this->couleurlien = strip_tags(trim($couleurlien));
		}
	}

	public function setcouleurlienblank($couleurlienblank)
	{
		$couleurlienblank = strval($couleurlienblank);
		if (strlen($couleurlienblank) <= self::LENCOULEUR) {
			$this->couleurlienblank = strip_tags(trim($couleurlienblank));
		}
	}

	public function setlien($lien)
	{
		if (!empty($lien) && strlen($lien) < self::LEN && is_string($lien)) {
			$lien = strip_tags(trim(strtolower($lien)));
			$lienlist = explode(", ", $lien);
			$this->lien = $lienlist;
		} else {
			$this->lien = [];
		}
	}

	public function setliento($liento)
	{
		if (is_array($liento)) {
			$this->liento = $liento;
		}


	}

	public function settemplate($template)
	{
		$template = strip_tags($template);
		if (strlen($template) == 0) {
			$template = 'NULL';
		}
		$this->template = $template;
	}


}


?>