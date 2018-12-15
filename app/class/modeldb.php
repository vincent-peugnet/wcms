<?php
class Modeldb extends Model
{
	protected $arttable;
	protected $database;
	/** @var \WFlywheel\Repository */
	protected $artstore;


	public function __construct()
	{
		$this->dbinit();
	}


	public function dbinit()
	{
		$this->database = new \JamesMoss\Flywheel\Config(Model::DATABASE_DIR, [
			'query_class' => "\WFlywheel\Query",
			'formatter' => new \WFlywheel\Formatter\JSON,
		]);
		$this->artstore = new \WFlywheel\Repository(Config::arttable(), $this->database);
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
