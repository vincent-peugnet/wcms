<?php
class Modeldb extends Model
{
	protected $database;
	/** @var \WFlywheel\Repository */
	protected $repo;


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
	}

	public function storeinit(string $repo)
	{
		$this->repo = new \WFlywheel\Repository($repo, $this->database);
	}

	public function list()
	{
		return $this->repo->getAllIds();
	}





}
