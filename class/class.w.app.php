<?php
class App
{
	private $bdd;
	private $session;
	private $arttable;


	const CONFIG_FILE = '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config.json';
	const CSS_READ_DIR = '..' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'read' . DIRECTORY_SEPARATOR;
	const SQL_READ_DIR = '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR;
	const MEDIA_DIR = '..' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR;
	const MEDIA_EXTENSIONS = array('jpeg', 'jpg', 'JPG', 'png', 'gif', 'mp3', 'mp4', 'mov', 'wav', 'flac', 'pdf');
	const MEDIA_TYPES = ['image', 'video', 'sound', 'other'];


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
			if ($this->session() >= self::EDITOR) {
				echo '<p>Error : ' . $e->getMessage() . '</p>';
				if ($this->session() == self::ADMIN) {
					echo '<p>Go to the <a href="?aff=admin">Admin Panel</a> to edit your database credentials</p>';
				} else {
					echo '<p>Logout and and come back with an <strong>admin password</strong> to edit the database connexions settings.</p>';
				}
			} else {
				echo '<p><a href=".">Homepage for admin login</a> (connect on the top right side)</p>';
			}
			exit;
		}

		return $caught;

	}

	public function settable(Config $config)
	{
		if (!empty($config->arttable())) {
			$this->arttable = $config->arttable();
		} else {
			echo '<h1>Table Error</h1>';

			if ($this->session() >= self::EDITOR) {
				if ($this->session() == self::ADMIN) {
					echo '<p>Go to the <a href="?aff=admin">Admin Panel</a> to select or add an Article table</p>';
				} else {
					echo '<p>Logout and and come back with an <strong>admin password</strong> to edit table settings.</p>';
				}
			} else {
				echo '<p><a href=".">Homepage for admin login</a> (connect on the top right side)</p>';
			}
			$caught = false;
			exit;
		}
	}

	public function bddinit(Config $config)
	{
		$test = $this->setbdd($config);
		if ($test) {
			$this->settable($config);
		}
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



	public function update(Art $art)
	{
		$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));

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




	//____________________________________________ L S T ______________________________



	public function getlister(array $selection = ['id'], array $opt = [])
	{
		// give an array using SELECTION columns and sort and desc OPTIONS 

		$default = ['tri' => 'id', 'desc' => 'DESC'];
		$opt = array_update($default, $opt);

		$list = [];
		$option = ['datecreation', 'titre', 'id', 'intro', 'datemodif', 'tag', 'secure'];
		if (is_array($selection) && is_string($opt['tri']) && strlen($opt['tri']) < 16 && is_string($opt['desc']) && strlen($opt['desc']) < 5 && in_array($opt['tri'], $option)) {

			$selection = implode(", ", $selection);

			$select = 'SELECT ' . $selection . ' FROM ' . $this->arttable . ' ORDER BY ' . $opt['tri'] . ' ' . $opt['desc'];
			$req = $this->bdd->query($select);
			while ($donnees = $req->fetch(PDO::FETCH_ASSOC)) {
				$list[] = new Art($donnees);
			}
			return $list;
		}
	}






	public function getlisteropt(Opt $opt)
	{

		$artlist = [];

		$select = 'SELECT ' . $opt->col('string') . ' FROM ' . $this->arttable;
		$req = $this->bdd->query($select);
		while ($donnees = $req->fetch(PDO::FETCH_ASSOC)) {
			$artlist[] = new Art($donnees);
		}
		return $artlist;

	}

	public function listcalclien(&$artlist)
	{
		foreach ($artlist as $art) {
			$art->calcliento($artlist);
		}
	}

	public function artcompare($art1, $art2, $method = 'id', $order = 1)
	{
		$result = ($art1->$method('sort') <=> $art2->$method('sort'));
		return $result * $order;

	}

	public function buildsorter($sortby, $order)
	{
		return function ($art1, $art2) use ($sortby, $order) {
			$result = $this->artcompare($art1, $art2, $sortby, $order);
			return $result;
		};
	}



	public function artlistsort(&$artlist, $sortby, $order = 1)
	{
		return usort($artlist, $this->buildsorter($sortby, $order));
	}






	public function filtertagfilter(array $artlist, array $tagchecked, $tagcompare = 'OR')
	{

		$filteredlist = [];
		foreach ($artlist as $art) {
			if (empty($tagchecked)) {
				$filteredlist[] = $art->id();
			} else {
				$inter = (array_intersect($art->tag('array'), $tagchecked));
				if ($tagcompare == 'OR') {
					if (!empty($inter)) {
						$filteredlist[] = $art->id();
					}
				} elseif ($tagcompare == 'AND') {
					if (!array_diff($tagchecked, $art->tag('array'))) {
						$filteredlist[] = $art->id();
					}
				}
			}
		}
		return $filteredlist;
	}

	public function filtersecure(array $artlist, $secure)
	{
		$filteredlist = [];
		foreach ($artlist as $art) {
			if ($art->secure() == intval($secure)) {
				$filteredlist[] = $art->id();
			} elseif (intval($secure) >= 4) {
				$filteredlist[] = $art->id();
			}
		}
		return $filteredlist;
	}


	public function lister()
	{
		$req = $this->bdd->query(' SELECT * FROM ' . $this->arttable . ' ORDER BY id ');
		$donnees = $req->fetchAll(PDO::FETCH_ASSOC);
		$req->closeCursor();
		return $donnees;

	}

	public function tag(array $artlist, $tagchecked)
	{
		$artcheckedlist = [];
		foreach ($artlist as $art) {
			if (in_array($tagchecked, $art->tag('array'))) {
				$artcheckedlist[] = $art;
			}
		}
		return $artcheckedlist;
	}

	public function taglist(array $artlist, array $tagcheckedlist)
	{
		$taglist = [];
		foreach ($tagcheckedlist as $tag) {
			$taglist[$tag] = $this->tag($artlist, $tag);
		}
		return $taglist;
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

	
	// __________________________________________ T A B L E ________________________________________________________


	public function tableexist($dbname, $tablename)
	{

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
			$arttables[] = $table['Tables_in_' . $dbname];
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

			$alter = "ALTER TABLE `$tablename`
			ADD PRIMARY KEY (`id`)";

			$req = $this->bdd->query($table);
			$req = $this->bdd->query($alter);

			return 'tablecreated';
		} else {
			return 'tablealreadyexist';
		}
	}



	public function tableduplicate($dbname, $arttable, $tablename)
	{
		$arttable = strip_tags($arttable);
		$tablename = str_clean($tablename);
		if ($this->tableexist($dbname, $arttable) && !$this->tableexist($dbname, $tablename)) {
			$duplicate = " CREATE TABLE `$tablename` LIKE `$arttable`;";
			$alter = "ALTER TABLE `$tablename` ADD PRIMARY KEY (`id`);";
			$insert = "INSERT `$tablename` SELECT * FROM `$arttable`;";


			$req = $this->bdd->query($duplicate . $alter . $insert);

			return 'tableduplicated';
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
			$extensions_autorisees = $this::MEDIA_EXTENSIONS;
			if (in_array($extension_upload, $extensions_autorisees)) {
				if (!file_exists($this::MEDIA_DIR . $id . '.' . $extension_upload)) {

					$extension_upload = strtolower($extension_upload);
					$uploadok = move_uploaded_file($file['media']['tmp_name'], $this::MEDIA_DIR . $id . '.' . $extension_upload);
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


	public function getmedia($entry, $dir)
	{
		$fileinfo = pathinfo($entry);

		$filepath = $fileinfo['dirname'] . '.' . $fileinfo['extension'];

		$donnees = array(
			'id' => str_replace('.' . $fileinfo['extension'], '', $fileinfo['filename']),
			'path' => $dir,
			'extension' => $fileinfo['extension']
		);



		return new Media($donnees);

	}

	public function getlistermedia($dir, $type = "all")
	{
		if ($handle = opendir($dir)) {
			$list = [];
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {

					$media = $this->getmedia($entry, $dir);


					$media->analyse();

					if (in_array($type, self::MEDIA_TYPES)) {
						if ($media->type() == $type) {
							$list[] = $media;
						}
					} else {
						$list[] = $media;
					}


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

	// public function changecss($lecturecss)
	// {
	// 	if (file_exists(self::CONFIG_FILE)) {
	// 		$current = file_get_contents(self::CONFIG_FILE);
	// 		$current = str_replace($this->lecturecss(), $lecturecss, $current);
	// 		file_put_contents(self::CONFIG_FILE, $current);
	// 		return 'css_change_ok';
	// 	} else {
	// 		return 'css_change_error';
	// 	}
	// }

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
				if (!file_exists($this::CSS_READ_DIR . $id . '.' . $extension_upload)) {

					$extension_upload = strtolower($extension_upload);
					$uploadok = move_uploaded_file($file['css']['tmp_name'], $this::CSS_READ_DIR . $id . '.' . $extension_upload);
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




	// ________________________________________________________ M A P ________________________________________________________


	public function map(array $getlister, $lb = PHP_EOL)
	{

		$map = "";
		$link = "";
		$style = "";
		foreach ($getlister as $item) {
			if($item->secure() == 2) {
				$style = $style . $lb . $item->id() . '{' . $item->titre() . '}';
			} elseif ($item->secure() == 1) {
				$style = $style . $lb . $item->id() . '(' . $item->titre() . ')';
				
			} else {
				$style = $style . $lb . $item->id() . '((' . $item->titre() . '))';
			}
			foreach ($item->lien('array') as $lien) {
				$map = $map . $lb . $item->id() . ' --> ' . $lien;
				$link = $link . $lb . 'click ' . $lien . ' "./?id=' . $lien . '"';
				
			}
			$link = $link . $lb . 'click ' . $item->id() . ' "./?id=' . $item->id() . '"';
		}
		return $map . $link . $style;

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