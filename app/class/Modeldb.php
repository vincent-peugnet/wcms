<?php

namespace Wcms;

use JamesMoss\Flywheel\Config;
use JamesMoss\Flywheel\DocumentInterface;
use JamesMoss\Flywheel\Document;
use LogicException;
use RuntimeException;
use Wcms\Exception\Databaseexception;
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
            throw new LogicException($e->getMessage());
        }
    }

    /**
     * Store Document but only if there is enough space left on disk
     *
     * @param Document $document   Flywheel Document
     *
     * @throws Databaseexception if minimum disk space is reached or if an error occured
     */
    protected function storedoc(DocumentInterface $document): void
    {
        if (!$this->isdiskfree()) {
            throw new Databaseexception('Not enough free space on disk');
        }
        if (!$this->repo->store($document)) {
            throw new Databaseexception('Impossible to store the document to database');
        }
    }

    /**
     * Update Document but only if there is enough space left on disk
     *
     * @param Document $document   Flywheel Document
     *
     * @throws Databaseexception if minimum disk space is reached or if an error occured
     */
    protected function updatedoc(DocumentInterface $document): void
    {
        if (!$this->isdiskfree()) {
            throw new Databaseexception('Not enough free space on disk');
        }
        if (!$this->repo->update($document)) {
            throw new Databaseexception('Impossible to update the document to database');
        }
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
     *
     * @throws LogicException if this failed
     */
    protected function storeinit(string $repo): void
    {
        try {
            $this->repo = new Repository($repo, $this->database);
        } catch (RuntimeException $e) {
            throw new LogicException($e->getMessage());
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
