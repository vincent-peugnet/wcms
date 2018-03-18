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
	private static $edit = 2;
	

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

	public function default()
	{
		$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));

		$this->settitre($this->id());
		$this->setsoustitre($this->id());
		$this->setintro('resumé');
		$this->settag('sans tag,');
		$this->setdatecreation($now);
		$this->setcss('display: inline;');
		$this->sethtml('contenu');
		$this->setsecure(2);
		$this->setcouleurtext('#000000');
		$this->setcouleurbkg('#FFFFFF');
		$this->setcouleurlien('#000000');
	}

	public function edit($session)
	{
		if ($session >= self::$edit) {

			?>
		<article>
		<form class="edit" action="?id=<?= $this->id() ?>" method="post">
		<label for="titre">Titre :</label>
		<input type="text" name="titre" id="titre" value="<?= $this->titre(); ?>">
		<label for="soustitre">Sous-titre :</label>
		<input type="text" name="soustitre" id="soustitre" value="<?= $this->soustitre(); ?>">
		<label for="intro">Introduction :</label>
		<input type="text" name="intro" id="intro" value="<?= $this->intro(); ?>">
		<label for="tag">Tag(s) :</label>
		<input type="text" name="tag" id="tag" value="<?= $this->tag(); ?>">
		<label for="css">Styles CSS :</label>
		<input type="text" name="css" id="css" value="<?= $this->css(); ?>">
		<label for="secure">Niveau de sécuritée :</label>
		<select name="secure" id="secure">
		<option value="0" <?= $this->secure() == 0 ? 'selected' : '' ?>>0</option>
		<option value="1" <?= $this->secure() == 1 ? 'selected' : '' ?>>1</option>
		<option value="2" <?= $this->secure() == 2 ? 'selected' : '' ?>>2</option>
		</select>
		<label for="couleurtext">Couleur du texte :</label>
		<input type="color" name="couleurtext" value="<?= $this->couleurtext() ?>" id="couleurtext">
		<label for="couleurbkg">Couleur de l'arrière plan :</label>
		<input type="color" name="couleurbkg" value="<?= $this->couleurbkg() ?>" id="couleurbkg">
		<label for="couleurlien">Couleur des liens :</label>
		<input type="color" name="couleurlien" value="<?= $this->couleurlien() ?>" id="couleurlien">
		<label for="html">Contenu :</label>
		<textarea name="html" id="html" ><?= $this->html(); ?></textarea>
		<input type="hidden" name="datecreation" value="<?= $this->datecreation('string'); ?>">
		<input type="hidden" name="id" value="<?= $this->id() ?>">
		<input type="hidden" name="action" value="update">
		<input type="submit" value="modifier">
		</form>
		</article>

		<?php

}

}

public function display($session)
{
	if ($session >= $this->secure()) {

		?>
		<style>
		article {
			background: <?= $this->couleurbkg() ?>;
			color: <?= $this->couleurtext() ?>;			
		}
		
		a {
			color: <?= $this->couleurlien() ?>;
		}
		<?= $this->css() ?>
		</style>
		<article>
		<h1><?= $this->titre() ?></h1>
		<h2><?= $this->soustitre() ?></h2>
		<h3><?= $this->intro() ?></h3>
		<p><?= $this->html() ?></p>
		</article>
		<?php

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

public function datecreation($option) {
	if ($option == 'string') {
		return $this->datecreation->format('Y-m-d H:i:s');
	} elseif($option == 'date') {
		return $this->datecreation;
	}
}


public function datemodif($option) {
	if ($option == 'string') {
		return $this->datemodif->format('Y-m-d H:i:s');
	} elseif($option == 'date') {
		return $this->datemodif;
	}
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