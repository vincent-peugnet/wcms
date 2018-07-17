<?php
class App
{
	private $bdd;
	private $session;
	private $arttable;


	const CONFIG_FILE = '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config.json';
	const CSS_READ_DIR = '..' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'lecture' . DIRECTORY_SEPARATOR;
	const SQL_READ_DIR = '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR;


	const ADMIN = 10;
	const EDITOR = 3;
	const INVITE = 2;
	const READ = 1;
	const FREE = 0;


// _____________________________________ C O N S T R U C T _________________________________



	public function __construct()
	{
		$this->setsession($this::FREE);
	}

	public function setbdd(Config $config)
	{
		$caught = true;

		try {
			$this->bdd = new PDO('mysql:host=' . $config->host() . ';dbname=' . $config->dbname() . ';charset=utf8', $config->user(), $config->password(), array(PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT));
			//$this->bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			$caught = false;
			echo '<h1>Error 500, database offline</h1>';
			echo '<p><a href=".">Homepage for admin login</a> (connect on the top right side)</p>';
			if ($this->session() >= 3) {
				echo '<p>Error : ' . $e->getMessage() . '</p>';
				if ($this->session() == 10) {
					echo '<p>Go to the <a href="?aff=admin">Admin Panel</a> to edit your database credentials</p>';
				} else {
					echo '<p>Logout and and come back with an <strong>admin password</strong> to edit the database connexions settings.</p>';
				}
			}
			exit;
		}
		if ($caught) {
			$this->arttable = $config->arttable();
		}

		return $caught;

	}


// _________________________________________ C O N F I G ____________________________________

	public function readconfig()
	{
		if (file_exists(self::CONFIG_FILE)) {
			$current = file_get_contents(self::CONFIG_FILE);
			$donnees = json_decode($current, true);
			return new Config($donnees);
		} else {
			return 0;
		}

	}

	public function createconfig(array $donnees)
	{
		return new Config($donnees);
	}


	public function savejson(string $json)
	{
		file_put_contents(self::CONFIG_FILE, $json);
	}






// ___________________________________________ A R T ____________________________________

	public function add(Art $art)
	{

		if ($this->exist($art->id())) {
			echo '<h4>cet id existe deja</h4>';
		} else {

			$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));

			$request = 'INSERT INTO ' . $this->arttable . '(id, titre, soustitre, intro, tag, datecreation, datemodif, css, html, secure, couleurtext, couleurbkg, couleurlien, couleurlienblank, lien, template) VALUES(:id, :titre, :soustitre, :intro, :tag, :datecreation, :datemodif, :css, :html, :secure, :couleurtext, :couleurbkg, :couleurlien, :couleurlienblank, :lien, :template)';

			$q = $this->bdd->prepare($request);

			$q->bindValue(':id', $art->id());
			$q->bindValue(':titre', $art->titre());
			$q->bindValue(':soustitre', $art->soustitre());
			$q->bindValue(':intro', $art->intro());
			$q->bindValue(':tag', $art->tag('string'));
			$q->bindValue(':datecreation', $now->format('Y-m-d H:i:s'));
			$q->bindValue(':datemodif', $now->format('Y-m-d H:i:s'));
			$q->bindValue(':css', $art->css());
			$q->bindValue(':html', $art->md());
			$q->bindValue(':secure', $art->secure());
			$q->bindValue(':couleurtext', $art->couleurtext());
			$q->bindValue(':couleurbkg', $art->couleurbkg());
			$q->bindValue(':couleurlien', $art->couleurlien());
			$q->bindValue(':couleurlienblank', $art->couleurlienblank());
			$q->bindValue(':lien', $art->lien('string'));
			$q->bindValue(':template', $art->template());

			$q->execute();
		}
	}

	public function delete(Art $art)
	{
		$req = $this->bdd->prepare('DELETE FROM ' . $this->arttable . ' WHERE id = :id ');
		$req->execute(array('id' => $art->id()));
		$req->closeCursor();
	}

	public function get($id)
	{
		$req = $this->bdd->prepare('SELECT * FROM ' . $this->arttable . ' WHERE id = :id ');
		$req->execute(array('id' => $id));
		$donnees = $req->fetch(PDO::FETCH_ASSOC);

		return new Art($donnees);

		$req->closeCursor();

	}

	public function getlister(array $selection = ['id', 'titre'], $tri = 'id', $desc = 'ASC')
	{
		$list = [];
		$option = ['datecreation', 'titre', 'id', 'intro', 'datemodif'];
		if (is_array($selection) && is_string($tri) && strlen($tri) < 16 && is_string($desc) && strlen($desc) < 5 && in_array($tri, $option)) {

			$selection = implode(", ", $selection);

			$select = 'SELECT ' . $selection . ' FROM ' . $this->arttable . ' ORDER BY ' . $tri . ' ' . $desc;
			$req = $this->bdd->query($select);
			while ($donnees = $req->fetch(PDO::FETCH_ASSOC)) {
				$list[] = new Art($donnees);
			}
			return $list;
		}
	}

	public function lister()
	{
		$req = $this->bdd->query(' SELECT * FROM ' . $this->arttable . ' ORDER BY id ');
		$donnees = $req->fetchAll(PDO::FETCH_ASSOC);
		return $donnees;

		$req->closeCursor();

	}

	public function count()
	{
		return $this->bdd->query(' SELECT COUNT(*) FROM ' . $this->arttable . ' ')->fetchColumn();
	}

	public function exist($id)
	{
		$req = $this->bdd->prepare(' SELECT COUNT(*) FROM ' . $this->arttable . ' WHERE id = :id ');
		$req->execute(array('id' => $id));
		$donnees = $req->fetch(PDO::FETCH_ASSOC);

		return (bool)$donnees['COUNT(*)'];
	}

	public function update(Art $art)
	{
		$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));
		$art->updatelien();

		$q = $this->bdd->prepare('UPDATE ' . $this->arttable . ' SET titre = :titre, soustitre = :soustitre, intro = :intro, tag = :tag, datecreation = :datecreation, datemodif = :datemodif, css = :css, html = :html, secure = :secure, couleurtext = :couleurtext, couleurbkg = :couleurbkg, couleurlien = :couleurlien, couleurlienblank = :couleurlienblank, lien = :lien, template = :template WHERE id = :id');

		$q->bindValue(':id', $art->id());
		$q->bindValue(':titre', $art->titre());
		$q->bindValue(':soustitre', $art->soustitre());
		$q->bindValue(':intro', $art->intro());
		$q->bindValue(':tag', $art->tag('string'));
		$q->bindValue(':datecreation', $art->datecreation('string'));
		$q->bindValue(':datemodif', $now->format('Y-m-d H:i:s'));
		$q->bindValue(':css', $art->css());
		$q->bindValue(':html', $art->md());
		$q->bindValue(':secure', $art->secure());
		$q->bindValue(':couleurtext', $art->couleurtext());
		$q->bindValue(':couleurbkg', $art->couleurbkg());
		$q->bindValue(':couleurlien', $art->couleurlien());
		$q->bindValue(':couleurlienblank', $art->couleurlienblank());
		$q->bindValue(':lien', $art->lien('string'));
		$q->bindValue(':template', $art->template());

		$q->execute();
	}

	public function tableexist($dbname, $tablename)
	{

		var_dump($dbname);
		var_dump($tablename);

		$req = $this->bdd->prepare('SELECT COUNT(*)
		FROM information_schema.tables
		WHERE table_schema = :dbname AND
			  table_name like :tablename');
		$req->execute(array(
			'dbname' => $dbname,
			'tablename' => $tablename
		));
		$donnees = $req->fetch(PDO::FETCH_ASSOC);
		$req->closeCursor();
		$exist = intval($donnees['COUNT(*)']);
		return $exist;




	}

	public function tablelist($dbname)
	{
		$request = 'SHOW TABLES IN ' . $dbname;
		$req = $this->bdd->query($request);
		$donnees = $req->fetchAll(PDO::FETCH_ASSOC);
		$req->closeCursor();

		$arttables = [];
		foreach ($donnees as $table) {
			$arttables[] = $table['Tables_in_wcms'];
		}
		return $arttables;


	}


	public function addtable($dbname, $tablename)
	{

		if (!$this->tableexist($dbname, $tablename)) {

			$table = "CREATE TABLE `$tablename` (
			`id` varchar(255) NOT NULL DEFAULT 'art',
			`titre` varchar(255) NOT NULL DEFAULT 'titre',
			`soustitre` varchar(255) NOT NULL DEFAULT 'soustitre',
			`intro` varchar(255) NOT NULL DEFAULT 'intro',
			`tag` varchar(255) NOT NULL DEFAULT 'sans tag,',
			`datecreation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`datemodif` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`css` text,
			`html` text,
			`secure` int(1) NOT NULL DEFAULT '0',
			`couleurtext` varchar(7) NOT NULL DEFAULT '#000000',
			`couleurbkg` varchar(7) NOT NULL DEFAULT '#ffffff',
			`couleurlien` varchar(7) NOT NULL DEFAULT '#2a3599',
			`couleurlienblank` varchar(7) NOT NULL DEFAULT '#2a8e99',
			`lien` varchar(255) DEFAULT NULL,
			`template` varchar(255) DEFAULT NULL
		  )";

			$alter = "ALTER TABLE `'.$tablename.'`
			ADD PRIMARY KEY (`id`)";

			$req = $this->bdd->query($table);
			$req->closeCursor();

			return 'tablecreated';
		} else {
			return 'tablealreadyexist';
		}
	}




// __________________________________________ M E D ________________________________________________________

	public function addmedia(array $file, $maxsize = 2 ** 24, $id)
	{
		$message = 'runing';
		$id = strtolower(strip_tags($id));
		$id = str_replace(' ', '_', $id);
		if (isset($file) and $file['media']['error'] == 0 and $file['media']['size'] < $maxsize) {
			$infosfichier = pathinfo($file['media']['name']);
			$extension_upload = $infosfichier['extension'];
			$extensions_autorisees = array('jpeg', 'jpg', 'JPG', 'png', 'gif', 'mp3', 'mp4', 'mov', 'wav', 'flac');
			if (in_array($extension_upload, $extensions_autorisees)) {
				if (!file_exists('..' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . $id . '.' . $extension_upload)) {

					$extension_upload = strtolower($extension_upload);
					$uploadok = move_uploaded_file($file['media']['tmp_name'], '..' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . $id . '.' . $extension_upload);
					if ($uploadok) {
						$message = 'uploadok';
					} else {
						$message = 'uploaderror';
					}
				} else {
					$message = 'filealreadyexist';

				}
			}
		} else {
			$message = 'filetoobig';

		}

		return $message;
	}


	public function getmedia($entry)
	{
		$fileinfo = pathinfo($entry);

		$filepath = $fileinfo['dirname'] . '.' . $fileinfo['extension'];

		list($width, $height, $type, $attr) = getimagesize($filepath);

		echo 'filepath : ' . $filepath;

		$donnes = array(
			'id' => str_replace('.' . $fileinfo['extension'], '', $fileinfo['filename']),
			'path' => $fileinfo['dirname'],
			'extension' => $fileinfo['extension']
		);



		return new Art($donnees);

	}

	public function getlistermedia($dir)
	{
		if ($handle = opendir($dir)) {
			$list = [];
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					$fileinfo = pathinfo($entry);

					$filepath = $dir . $fileinfo['filename'] . '.' . $fileinfo['extension'];

					list($width, $height, $type, $attr) = getimagesize($filepath);
					$filesize = filesize($filepath);

					$donnees = array(
						'id' => str_replace('.' . $fileinfo['extension'], '', $fileinfo['filename']),
						'path' => $fileinfo['dirname'],
						'extension' => $fileinfo['extension'],
						'size' => $filesize,
						'width' => $width,
						'height' => $height
					);

					$list[] = new Media($donnees);

				}
			}
			return $list;
		}

		return $list;

	}




	//_________________________________________________________ R E C ________________________________________________________


	public function getlisterrecord($dir)
	{
		if ($handle = opendir($dir)) {
			$list = [];
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					$fileinfo = pathinfo($entry);

					$filepath = $dir . $fileinfo['filename'] . '.' . $fileinfo['extension'];

					list($width, $height, $type, $attr) = getimagesize($filepath);
					$filesize = filesize($filepath);

					$donnees = array(
						'id' => str_replace('.' . $fileinfo['extension'], '', $fileinfo['filename']),
						'path' => $fileinfo['dirname'],
						'extension' => $fileinfo['extension'],
						'size' => $filesize
					);

					$list[] = new Record($donnees);

				}
			}
		}

		return $list;



	}



	//_________________________________________________________ A D M ________________________________________________________

	public function changecss($lecturecss)
	{
		if (file_exists(self::CONFIG_FILE)) {
			$current = file_get_contents(self::CONFIG_FILE);
			$current = str_replace($this->lecturecss(), $lecturecss, $current);
			file_put_contents(self::CONFIG_FILE, $current);
			return 'ccss_change_ok';
		} else {
			return 'ccss_change_error';
		}
	}

	public function addcss(array $file, $maxsize = 2 ** 24, $id)
	{
		$message = 'runing';
		$id = strtolower(strip_tags($id));
		$id = str_replace(' ', '_', $id);
		if (isset($file) and $file['css']['error'] == 0 and $file['css']['size'] < $maxsize) {
			$infosfichier = pathinfo($file['css']['name']);
			$extension_upload = $infosfichier['extension'];
			$extensions_autorisees = array('css');
			if (in_array($extension_upload, $extensions_autorisees)) {
				if (!file_exists('..' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'lecture' . DIRECTORY_SEPARATOR . $id . '.' . $extension_upload)) {

					$extension_upload = strtolower($extension_upload);
					$uploadok = move_uploaded_file($file['css']['tmp_name'], '..' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'lecture' . DIRECTORY_SEPARATOR . $id . '.' . $extension_upload);
					if ($uploadok) {
						$message = 'uploadok';
					} else {
						$message = 'uploaderror';
					}
				} else {
					$message = 'filealreadyexist';

				}
			}
		} else {
			$message = 'filetoobig';

		}

		return $message;
	}

	public function dirlist($dir, $extension)
	{
		if ($handle = opendir($dir)) {
			$list = [];
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != ".." && pathinfo($entry)['extension'] == $extension) {

					$list[] = $entry;

				}
			}
			return $list;
		}
	}

	public function downloadtable()
	{

	}




	//_________________________________________________________ S E S ________________________________________________________

	public function login($pass, $config)
	{
		if (strip_tags($pass) == $config->admin()) {
			return $level = self::ADMIN;
		} elseif (strip_tags($pass) == $config->read()) {
			return $level = self::READ;
		} elseif (strip_tags($pass) == $config->editor()) {
			return $level = self::EDITOR;
		} elseif (strip_tags($pass) == $config->invite()) {
			return $level = self::INVITE;
		}
	}

	public function logout()
	{
		return $level = 0;
	}

	// ________________________________________________________ S E T ___________________________________________________


	public function setsession($session)
	{
		$this->session = $session;
	}



	
	//_________________________________________________________ G E T ________________________________________________________

	public function session()
	{
		return $this->session;
	}


}
?>