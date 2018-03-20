<?php

use Michelf\Markdown;
use Michelf\MarkdownExtra;


class Art
{
	private $id;
	private $titre;
	private $soustitre;
	private $intro;
	private $tag;
	private $datecreation;
	private $datemodif;
	private $css;
	private $html;
	private $secure;
	private $couleurtext;
	private $couleurbkg;
	private $couleurlien;

	private static $len = 255;
	private static $lenhtml = 65535;
	private static $securemax = 2;
	private static $lencouleur = 7;
	

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

		$this->settitre($this->id());
		$this->setsoustitre($this->id());
		$this->setintro('resumé');
		$this->settag('sans tag,');
		$this->setdatecreation($now);
		$this->setcss('article {}');
		$this->sethtml('contenu');
		$this->setsecure(2);
		$this->setcouleurtext('#000000');
		$this->setcouleurbkg('#FFFFFF');
		$this->setcouleurlien('#000000');
	}

		// _____________________________________________________ G E T ____________________________________________________

	public function id()
	{
		return $this->id;
	}

	public function titre()
	{
		return $this->titre;
	}

	public function soustitre()
	{
		return $this->soustitre;
	}

	public function intro()
	{
		return $this->intro;
	}

	public function tag($option)
	{
		if ($option == 'string') {
			$tag = implode(", ", $this->tag);
		} elseif ($option == 'array') {
			$tag = $this->tag;
		}
		return $tag;
	}

	public function datecreation($option)
	{
		if ($option == 'string') {
			return $this->datecreation->format('Y-m-d H:i:s');
		} elseif ($option == 'date') {
			return $this->datecreation;
		}
	}


	public function datemodif($option)
	{
		if ($option == 'string') {
			return $this->datemodif->format('Y-m-d H:i:s');
		} elseif ($option == 'date') {
			return $this->datemodif;
		}
	}

	public function css()
	{
		return $this->css;
	}

	public function html($option)
	{
		if ($option == 'md') {
			return $this->html;
		} elseif ($option == 'html') {
			$html = MarkdownExtra::defaultTransform($this->html);
			$htmla = str_replace('class="b"', ' target="_blank" ', $html);
			return $htmla;
		}
	}

	public function secure()
	{
		return $this->secure;
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



		// _____________________________________________________ S E T ____________________________________________________

	public function setid($id)
	{
		if (strlen($id) < self::$len and is_string($id)) {
			$this->id = strip_tags(strtolower(str_replace(" ", "", $id)));
		}
	}

	public function settitre($titre)
	{
		if (strlen($titre) < self::$len and is_string($titre)) {
			$this->titre = strip_tags(trim($titre));
		}
	}

	public function setsoustitre($soustitre)
	{
		if (strlen($soustitre) < self::$len and is_string($soustitre)) {
			$this->soustitre = strip_tags(trim($soustitre));
		}
	}

	public function setintro($intro)
	{
		if (strlen($intro) < self::$len and is_string($intro)) {
			$this->intro = strip_tags(trim($intro));
		}
	}

	public function settag($tag)
	{
		if (strlen($tag) < self::$len and is_string($tag)) {
			$tag = strip_tags(trim(strtolower($tag)));
			$taglist = explode(", ", $tag);
			$this->tag = $taglist;
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

	public function setcss($css)
	{
		if (strlen($css) < self::$len and is_string($css)) {
			$this->css = strip_tags(trim(strtolower($css)));
		}
	}

	public function sethtml($html)
	{
		if (strlen($html) < self::$lenhtml and is_string($html)) {
			$this->html = $html;
		}
	}

	public function setsecure($secure)
	{
		if ($secure >= 0 and $secure <= self::$securemax) {
			$this->secure = intval($secure);
		}
	}

	public function setcouleurtext($couleurtext)
	{
		$couleurtext = strval($couleurtext);
		if (strlen($couleurtext) <= self::$lencouleur) {
			$this->couleurtext = strip_tags(trim($couleurtext));
		}
	}

	public function setcouleurbkg($couleurbkg)
	{
		$couleurbkg = strval($couleurbkg);
		if (strlen($couleurbkg) <= self::$lencouleur) {
			$this->couleurbkg = strip_tags(trim($couleurbkg));
		}
	}

	public function setcouleurlien($couleurlien)
	{
		$couleurlien = strval($couleurlien);
		if (strlen($couleurlien) <= self::$lencouleur) {
			$this->couleurlien = strip_tags(trim($couleurlien));
		}
	}


}


?>