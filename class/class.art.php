<h4>class.art</h4>
<?php

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

	public function tag()
	{
		return $this->tag;
	}

	public function datecreation()
	{
		return $this->datecreation;
	}

	public function datemodif()
	{
		return $this->datemodif;
	}

	public function css()
	{
		return $this->css;
	}

	public function html()
	{
		return $this->html;
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
			$this->tag = strip_tags(trim(strtolower($tag)));
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