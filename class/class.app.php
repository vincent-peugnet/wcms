<h4>class.app</h4>
<?php
class App
{
	private $bdd;

	public function __construct()
	{
		// try {
		// 	$this->bdd = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'] . ';charset=utf8', $config['user'], $config['password']);
		// } catch (Exeption $e) {
		// 	die('Erreur : ' . $e->getMessage());
		// }

		try {
			$this->bdd = new PDO('mysql:host=localhost;dbname=wcms;charset=utf8', 'root', '');
		} catch (Exeption $e) {
			die('Erreur : ' . $e->getMessage());
		}
	}

	public function add(Art $art)
	{
// tester l'existence de l'id
		$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));

		$q = $this->bdd->prepare('INSERT INTO art(id, titre, soustitre, intro, tag, datecreation, datemodif, css, html, secure, couleurtext, couleurbkg, couleurlien) VALUES(:id, :titre, :soustitre, :intro, :tag, :datecreation, :datemodif, :css, :html, :secure, :couleurtext, :couleurbkg, :couleurlien)');

		$q->bindValue(':id', $art->id());
		$q->bindValue(':titre', $art->titre());
		$q->bindValue(':soustitre', $art->soustitre());
		$q->bindValue(':intro', $art->intro());
		$q->bindValue(':tag', $art->tag());
		$q->bindValue(':datecreation', $now->format('Y-m-d H:i:s'));
		$q->bindValue(':datemodif', $now->format('Y-m-d H:i:s'));
		$q->bindValue(':css', $art->css());
		$q->bindValue(':html', $art->html());
		$q->bindValue(':secure', $art->secure());
		$q->bindValue(':couleurtext', $art->couleurtext());
		$q->bindValue(':couleurbkg', $art->couleurbkg());
		$q->bindValue(':couleurlien', $art->couleurlien());

		$q->execute();
	}

	public function delete(Art $art)
	{
		$this->bdd->exec('DELETE FROM art WHERE id = ' . $art->id());
	}

	public function get($id)
	{
		$q = $this->bdd->query('SELECT * FROM art WHERE id = ' . $id);
		$donnees = $q->fetch(PDO::FETCH_ASSOC);

		return new Art($donnees);
	}

	public function getlist()
	{
		$listart = [];

		$q = $this->bdd->query('SELECT titre, soustitre, intro FROM art ORDER BY titre');

		while ($donnees = $q->fetch(PDO::FETCH_ASSOC)) {
			$listart[] = new art($donnees);
		}

		return $listart;
	}

	public function count()
	{
		return $this->bdd->query('SELECT COUNT(*) FROM art')->fetchColumn();
	}

	// public function exist($id)
	// {
	// 	$r = $this->bdd->query('SELECT COUNT(*) AS art FROM art WHERE id = '.$id);
	// 	return $donnees = $r->fetch(PDO::FETCH_ASSOC);
	// }

	public function exist($id)
	{
		return $this->bdd->query('SELECT COUNT(*) FROM art WHERE id = ' . $id)->fetchColumn();
	}

	public function update(Art $art)
	{
		$q = $this->bdd->prepare('UPDATE art SET titre = :titre, soustitre = :soustitre, intro = :intro, tag = :tag, datecreation = :datecreation, datemodif = :datemodif, cass = :css, html = :html, secure = :secure, couleurtext = :couleurtext, couleurbkg = :couleurbkg, couleurlien = :couleurlien WHERE id = :id');

		$q->bindValue(':id', $art->id());
		$q->bindValue(':titre', $art->titre());
		$q->bindValue(':soustitre', $art->soustitre());
		$q->bindValue(':intro', $art->intro());
		$q->bindValue(':tag', $art->tag());
		$q->bindValue(':datecreation', $art->datecreation());
		$q->bindValue(':datemodif', $art->datemodif());
		$q->bindValue(':css', $art->css());
		$q->bindValue(':html', $art->html());
		$q->bindValue(':secure', $art->secure(), PDO::PARAM_INT);
		$q->bindValue(':couleurtext', $art->tag());
		$q->bindValue(':couleurbkgt', $art->couleurbkgt());
		$q->bindValue(':couleurlien', $art->couleurlien());

		$q->execute();
	}


}
?>