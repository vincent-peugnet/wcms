<?php

namespace Wcms;

use JamesMoss\Flywheel;
use RuntimeException;
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
        try {
            $this->repo = new Repository($repo, $this->database);
        } catch (RuntimeException $e) {
            self::sendflashmessage("error while database initialisation: " . $e->getMessage(), self::FLASH_ERROR);
        }
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
