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

			$q = $this->bdd->prepare('INSERT INTO art(id, titre, soustitre, intro, tag, datecreation, datemodif, css, html, secure, couleurtext, couleurbkg, couleurlien, couleurlienblank, lien) VALUES(:id, :titre, :soustitre, :intro, :tag, :datecreation, :datemodif, :css, :html, :secure, :couleurtext, :couleurbkg, :couleurlien, :couleurlienblank, :lien)');

			$q->bindValue(':id', $art->id());
			$q->bindValue(':titre', $art->titre());
			$q->bindValue(':soustitre', $art->soustitre());
			$q->bindValue(':intro', $art->intro());
			$q->bindValue(':tag', $art->tag('string'));
			$q->bindValue(':datecreation', $now->format('Y-m-d H:i:s'));
			$q->bindValue(':datemodif', $now->format('Y-m-d H:i:s'));
			$q->bindValue(':css', $art->css());
			$q->bindValue(':html', $art->html('md'));
			$q->bindValue(':secure', $art->secure());
			$q->bindValue(':couleurtext', $art->couleurtext());
			$q->bindValue(':couleurbkg', $art->couleurbkg());
			$q->bindValue(':couleurlien', $art->couleurlien());
			$q->bindValue(':couleurlienblank', $art->couleurlienblank());
			$q->bindValue(':lien', $art->lien('string'));

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

	public function getlister(array $selection, $tri)
	{
		$list = [];
		$option = ['datecreation', 'titre', 'id', 'intro', 'datemodif'];
		if (is_array($selection) && is_string($tri) && strlen($tri) < 12 && in_array($tri, $option)) {

			$selection = implode(", ", $selection);

			$select = 'SELECT ' . $selection . ' FROM art ORDER BY ' . $tri;
			$req = $this->bdd->query($select);
			while ($donnees = $req->fetch(PDO::FETCH_ASSOC)) {
				$list[] = new Art($donnees);
			}
			return $list;
		}
	}

	public function lister()
	{
		$req = $this->bdd->query(' SELECT * FROM art ORDER BY id ');
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

	public function introlien(Art $art)
	{
		$html = $art->html('html');
		foreach ($art->lien('array') as $id) {
			$title = '';
			foreach ($this->getlister(['id', 'intro'], 'id') as $item) {
				if ($item->id() == $id) {
					$title = $item->intro();
				}
			}

			$lien = 'href="?id=' . $id . '"';
			$titlelien = ' title="' . $title . '" ' . $lien;
			$html = str_replace($lien, $titlelien, $html);
		}
		return $html;
	}

	public function update(Art $art)
	{
		$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));
		$art->updatelien();

		var_dump($now);

		$q = $this->bdd->prepare('UPDATE art SET titre = :titre, soustitre = :soustitre, intro = :intro, tag = :tag, datecreation = :datecreation, datemodif = :datemodif, css = :css, html = :html, secure = :secure, couleurtext = :couleurtext, couleurbkg = :couleurbkg, couleurlien = :couleurlien, couleurlienblank = :couleurlienblank, lien = :lien WHERE id = :id');

		$q->bindValue(':id', $art->id());
		$q->bindValue(':titre', $art->titre());
		$q->bindValue(':soustitre', $art->soustitre());
		$q->bindValue(':intro', $art->intro());
		$q->bindValue(':tag', $art->tag('string'));
		$q->bindValue(':datecreation', $art->datecreation('string'));
		$q->bindValue(':datemodif', $now->format('Y-m-d H:i:s'));
		$q->bindValue(':css', $art->css());
		$q->bindValue(':html', $art->html('md'));
		$q->bindValue(':secure', $art->secure());
		$q->bindValue(':couleurtext', $art->couleurtext());
		$q->bindValue(':couleurbkg', $art->couleurbkg());
		$q->bindValue(':couleurlien', $art->couleurlien());
		$q->bindValue(':couleurlienblank', $art->couleurlienblank());
		$q->bindValue(':lien', $art->lien('string'));

		$q->execute();
	}

	public function addmedia(array $file, $maxsize, $id)
	{
		$maxsize = 2 ** 40;
		$id = strtolower(strip_tags($id));
		if (isset($file) and $file['media']['error'] == 0 and $file['media']['size'] < $maxsize) {
			$infosfichier = pathinfo($file['media']['name']);
			$extension_upload = $infosfichier['extension'];
			$extensions_autorisees = array('jpeg', 'jpg', 'JPG', 'png', 'gif', 'mp3', 'mp4', 'mov', 'wav', 'flac');
			if (in_array($extension_upload, $extensions_autorisees)) {
				if (!file_exists('../media/' . $id . '.' . $extension_upload)) {

					$uploadok = move_uploaded_file($file['media']['tmp_name'], '../media/' . $id . '.' . $extension_upload);
					if ($uploadok) {
						header('Location: ./?message=uploadok');
					} else {
						header('Location: ./?message=uploaderror');
					}
				} else {
					header('Location: ./?message=filealreadyexist');

				}
			}
		} else {
			header('Location: ./?message=filetoobig');

		}
	}

	//_________________________________________________________ S E S ________________________________________________________

	public function login($pass)
	{
		if (strip_tags($pass) == $this->admin) {
			var_dump($this->admin);
			return $level = 2;
		} elseif (strip_tags($pass) == $this->secure) {
			return $level = 1;
		}
	}

	public function logout()
	{
		return $level = 0;
	}

}
?>