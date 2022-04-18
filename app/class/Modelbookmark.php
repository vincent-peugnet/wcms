<?php

namespace Wcms;

use JamesMoss\Flywheel\Document;
use RuntimeException;

class Modelbookmark extends Modeldb
{
    public const BOOKMARK_REPO_NAME = 'bookmark';

    public function __construct()
    {
        parent::__construct();
        $this->storeinit(self::BOOKMARK_REPO_NAME);
    }

    /**
     * @return Bookmark[] associative array of Bookmark objects `id => Bookmark`
     */
    public function getlister(): array
    {
        $bookmarks = [];
        $list = $this->repo->findAll();
        foreach ($list as $bookmarkdata) {
            try {
                $bookmarks[$bookmarkdata->id] = new Bookmark($bookmarkdata);
            } catch (RuntimeException $e) {
                Logger::error("Error while reading bookmark \"$bookmarkdata->id\" from database : " . $e->getMessage());
            }
        }
        return $bookmarks;
    }


    /**
     * @param Bookmark $bookmark
     * @throws RuntimeException when ID is empty
     */
    public function add(Bookmark $bookmark): bool
    {
        if (empty($bookmark->id())) {
            throw new RuntimeException("Invalid ID");
        }
        $bookmarkdata = new Document($bookmark->dry());
        $bookmarkdata->setId($bookmark->id());
        return $this->repo->store($bookmarkdata);
    }
}
