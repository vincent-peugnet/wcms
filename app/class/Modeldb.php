<?php

namespace Wcms;

use JamesMoss\Flywheel;
use Wcms\Flywheel\Formatter\JSON;
use Wcms\Flywheel\Query;
use Wcms\Flywheel\Repository;

class Modeldb extends Model
{
	protected $database;
	/** @var Repository */
	protected $repo;


	public function __construct()
	{
		$this->dbinit();
	}


	public function dbinit()
	{
		$this->database = new Flywheel\Config(Model::DATABASE_DIR, [
			'query_class' => Query::class,
			'formatter' => new JSON,
		]);
	}

	public function storeinit(string $repo)
	{
		$this->repo = new Repository($repo, $this->database);
	}

	public function list()
	{
		return $this->repo->getAllIds();
	}





}
