<?php
class Modeldb extends Model
{
	/** @var PDO */
	protected $bdd;
	protected $arttable;
	protected $database;
	protected $artstore;


	public function __construct()
	{
		$this->dbinit();
	}


	public function dbinit()
	{
		$this->database = new \JamesMoss\Flywheel\Config(Model::DATABASE_DIR);
		$this->artstore = new \JamesMoss\Flywheel\Repository(Config::arttable(), $this->database);
	}

	
	public function getlister()
	{
		$artlist = [];
		$list = $this->artstore->findAll();
		foreach ($list as $artdata) {
			$artlist[$artdata->id] = new Art2($artdata);
		}
		return $artlist;
	}

	public function list()
	{
		return $this->artstore->getAllIds();
	}

	public function getlisterid(array $idlist = [])
	{
		$artdatalist = $this->artstore->query()
		->where('__id', 'IN', $idlist)
		->execute();

		$artlist = [];
		foreach ($artdatalist as $id => $artdata) {
			$artlist[$id] = new Art2($artdata);
		}
		return $artlist;
	}



}
