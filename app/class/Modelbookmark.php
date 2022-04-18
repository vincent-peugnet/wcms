<?php

namespace Wcms;

use InvalidArgumentException;
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
     * @return Bookmark[]                   associative array of Bookmark objects `id => Bookmark`
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
     * @param string|Bookmark $id           Can be an User object or a string ID
     * @throws RuntimeException             If Bookmark cant be founded
     * @throws InvalidArgumentException     If argument isn't a string or a bookmark
     *
     * @return Bookmark                     User object or false in case of error
     */
    public function get($id): Bookmark
    {
        if ($id instanceof Bookmark) {
            $id = $id->id();
        }
        if (is_string($id)) {
            $bookmarkdata = $this->repo->findById($id);
            if ($bookmarkdata !== false) {
                return new Bookmark($bookmarkdata);
            } else {
                throw new RuntimeException("Could not find Bookmark with the following ID: \"$id\"");
            }
        } else {
            throw new InvalidArgumentException(
                "Argument of Modelbookmark->get() should be a ID string or a Bookmark object"
            );
        }
    }


    /**
     * @param Bookmark $bookmark
     * @throws RuntimeException             when ID is empty
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

    /**
     * @param Bookmark $bookmark            Bookmark to be deleted
     * @throws RuntimeException             when removing bookmark failed
     */
    public function delete(Bookmark $bookmark): void
    {
        $success = $this->repo->delete($bookmark->id());
        if (!$success) {
            throw new RuntimeException("Bookmark \"" . $bookmark->id() . "\" could not be deleted ");
        }
    }
}
