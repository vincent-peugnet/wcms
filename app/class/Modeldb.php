<?php

namespace Wcms;

use InvalidArgumentException;
use JamesMoss\Flywheel\Config;
use JamesMoss\Flywheel\DocumentInterface;
use JamesMoss\Flywheel\Document;
use RuntimeException;
use Wcms\Flywheel\Formatter\JSON;
use Wcms\Flywheel\Query;
use Wcms\Flywheel\Repository;

class Modeldb extends Model
{
    protected Config $database;
    /** @var Repository */
    protected Repository $repo;

    /**
     * Minimal disk space needed to authorize database writing.
     * 2^18 o = 256 kio
    */
    public const MINIMAL_DISK_SPACE = 2 ** 18;

    public function __construct()
    {
        $this->dbinit();
    }

    /**
     * Check if database directory have at least the minimal free disk space required left.
     *
     * @return bool                         True if enought space left, otherwise False
     */
    protected function isdiskfree(): bool
    {
        try {
            return (disk_free_space_ex(self::DATABASE_DIR) > self::MINIMAL_DISK_SPACE);
        } catch (RuntimeException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * Store Document but only if there is enough space left on disk
     *
     * @param Document $document   Flywheel Document
     * @return bool                         True in case of success, otherwise false
     *
     * @todo use exceptions to create a disctinction between differents possible problems
     */
    protected function storedoc(DocumentInterface $document): bool
    {
        if (!$this->isdiskfree()) {
            Logger::error("Not enough free space on disk to store datas in database");
            return false;
        }
        return $this->repo->store($document);
    }

    /**
     * Update Document but only if there is enough space left on disk
     *
     * @param Document $document   Flywheel Document
     * @return bool                         True in case of success, otherwise false
     *
     * @todo use exceptions to create a disctinction between differents possible problems
     */
    protected function updatedoc(DocumentInterface $document): bool
    {
        if (!$this->isdiskfree()) {
            Logger::error("Not enough free space on disk to update datas in database");
            return false;
        }
        return $this->repo->update($document);
    }

    /**
     * Init database config
     *
     * @param string $dir                   Directory where repo is stored.
     */
    protected function dbinit(string $dir = Model::DATABASE_DIR): void
    {
        $this->database = new Config($dir, [
            'query_class' => Query::class,
            'formatter' => new JSON(),
        ]);
    }

    /**
     * Init store.
     *
     * @param string $repo                  Name of the repo
     */
    protected function storeinit(string $repo): void
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
     * @return string[]                     array of ID strings
     */
    public function list(): array
    {
        return $this->repo->getAllIds();
    }
}
