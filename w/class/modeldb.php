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
		$this->database = new \JamesMoss\Flywheel\Config(__DIR__ .'/../../w_database');
		$this->artstore = new \JamesMoss\Flywheel\Repository(Config::arttable(), $this->database);
	}


}
