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


    public function dbinit($dir = Model::DATABASE_DIR)
    {
        $this->database = new Flywheel\Config($dir, [
            'query_class' => Query::class,
            'formatter' => new JSON(),
        ]);
    }

    public function storeinit(string $repo)
    {
        $this->repo = new Repository($repo, $this->database);
    }

    /**
     * List every IDs of a database
     *
     * @return string[] array of ID strings
     */
    public function list(): array
    {
        return $this->repo->getAllIds();
    }
}
