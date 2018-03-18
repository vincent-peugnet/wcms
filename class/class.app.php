<?php
class App
{
	private $bdd;
	private $admin;
	private $secure;

	public function __construct($config)
	{
		$this->admin = $config['admin'];
		$this->secure = $config['secure'];

		try {
			$this->bdd = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'] . ';charset=utf8', $config['user'], $config['password']);
		} catch (Exeption $e) {
			die('Erreur : ' . $e->getMessage());
		}
	}

	public function add(Art $art)
	{

		if ($this->exist($art->id())) {
			echo '<h4>cet id existe deja</h4>';
		} else {

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
			$q->bindValue(':html', $art->html('md'));
			$q->bindValue(':secure', $art->secure());
			$q->bindValue(':couleurtext', $art->couleurtext());
			$q->bindValue(':couleurbkg', $art->couleurbkg());
			$q->bindValue(':couleurlien', $art->couleurlien());

			$q->execute();
		}
	}

	public function delete(Art $art)
	{
		$req = $this->bdd->prepare('DELETE FROM art WHERE id = :id ');
		$req->execute(array('id' => $art->id()));
		$req->closeCursor();
	}

	public function get($id)
	{
		$req = $this->bdd->prepare('SELECT * FROM art WHERE id = :id ');
		$req->execute(array('id' => $id));
		$donnees = $req->fetch(PDO::FETCH_ASSOC);

		return new Art($donnees);

		$req->closeCursor();

	}

	public function getlist()
	{
		$list = [];

		$req = $this->bdd->query('SELECT * FROM art ORDER BY id');
		while ($donnees = $req->fetch(PDO::FETCH_ASSOC)) {
			$list[] = new Art($donnees);
		}
		return $list;
	}

	public function list()
	{
		$req = $this->bdd->query('SELECT * FROM art ORDER BY id');
		$donnees = $req->fetchAll(PDO::FETCH_ASSOC);
		return $donnees;
		
		$req->closeCursor();

	}

	public function count()
	{
		return $this->bdd->query(' SELECT COUNT(*) FROM art ')->fetchColumn();
	}

	public function exist($id)
	{
		$req = $this->bdd->prepare(' SELECT COUNT(*) FROM art WHERE id = :id ');
		$req->execute(array('id' => $id));
		$donnees = $req->fetch(PDO::FETCH_ASSOC);

		return (bool)$donnees['COUNT(*)'];
	}

	public function update(Art $art)
	{
		$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));

		$q = $this->bdd->prepare('UPDATE art SET titre = :titre, soustitre = :soustitre, intro = :intro, tag = :tag, datecreation = :datecreation, datemodif = :datemodif, css = :css, html = :html, secure = :secure, couleurtext = :couleurtext, couleurbkg = :couleurbkg, couleurlien = :couleurlien WHERE id = :id');
	
		$q->bindValue(':id', $art->id());
		$q->bindValue(':titre', $art->titre());
		$q->bindValue(':soustitre', $art->soustitre());
		$q->bindValue(':intro', $art->intro());
		$q->bindValue(':tag', $art->tag());
		$q->bindValue(':datecreation', $art->datecreation('string'));
		$q->bindValue(':datemodif', $now->format('Y-m-d H:i:s'));
		$q->bindValue(':css', $art->css());
		$q->bindValue(':html', $art->html('md'));
		$q->bindValue(':secure', $art->secure());
		$q->bindValue(':couleurtext', $art->couleurtext());
		$q->bindValue(':couleurbkg', $art->couleurbkg());
		$q->bindValue(':couleurlien', $art->couleurlien());

		$q->execute();
	}

	//_________________________________________________________ S E S ________________________________________________________

	public function login($pass)
	{
		if(strip_tags($pass) == $this->admin)
		{
			var_dump($this->admin);
			$_SESSION['level'] = 2;
		}
		elseif(strip_tags($pass) == $this->secure)
		{
			$_SESSION['level'] = 1;			
		}
	}

	public function logout()
	{
		$_SESSION['level'] = 0;
	}

}
?>