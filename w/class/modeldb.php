<?php
class Modeldb extends Model
{
	/** @var PDO */
    protected $bdd;
	protected $arttable;
	protected $database;
	protected $artstore;
	

    public function __construct() {
		//$this->setbdd();
		//self::setdb();

		$this->dbinit();
	}

	
    public function dbinit()
    {
        $this->database = new Dbengine(__DIR__ . '/../../w_database');
        $this->artstore = $this->database->store(Config::arttable());

    }

	


    public function setbdd()
	{
		$caught = true;

		try {
			$this->bdd = new PDO('mysql:host=' . Config::host() . ';dbname=' . Config::dbname() . ';charset=utf8', Config::user(), Config::password(), array(PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT));
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


	public static function setdb()
	{
		$caught = true;

		try {
			self::$db = new PDO('mysql:host=' . Config::host() . ';dbname=' . Config::dbname() . ';charset=utf8', Config::user(), Config::password(), array(PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT));
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
		if (!empty(Config::arttable())) {
			$this->arttable = Config::arttable();
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



}
