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
     * @param string $user                  user field in Bookmark
     * @param string $comp                  comparaison operator
     * @return Bookmark[]                   array of Bookmarks objects with IDs as key
     */
    public function getlisterbyuserid(string $user, $comp = '==='): array
    {
        $bookmarkdatas = $this->repo->query()
            ->where('user', $comp, $user)
            ->execute();

        $bookmarks = [];
        foreach ($bookmarkdatas as $bookmarkdata) {
            try {
                $bookmarks[$bookmarkdata->id] = new Bookmark($bookmarkdata);
            } catch (RuntimeException $e) {
                Logger::error("Error while reading bookmark \"$bookmarkdata->id\" from database : " . $e->getMessage());
            }
        }
        return $bookmarks;
    }



    /**
     * Return all public bookmarks
     *
     * @return Bookmark[]                   array of Bookmarks objects with IDs as key
     */
    public function getlisterpublic(): array
    {
        return $this->getlisterbyuserid("");
    }



    /**
     * Return all personal bookmarks from a specific user
     *
     * @param User $user                    User owning bookmarks
     * @return Bookmark[]                   array of Bookmarks objects with IDs as key
     */
    public function getlisterbyuser(User $user): array
    {
        return $this->getlisterbyuserid($user->id());
    }



    /**
     * @param string|Bookmark $id           Can be an User object or a string ID
     * @throws RuntimeException             If Bookmark cant be founded
     *
     * @return Bookmark                     User object or false in case of error
     */
    public function get($id): Bookmark
    {
        $bookmarkdata = $this->repo->findById($this->id($id));
        if ($bookmarkdata !== false) {
            return new Bookmark($bookmarkdata);
        } else {
            throw new RuntimeException("Could not find Bookmark with the following ID: \"$id\"");
        }
    }


    /**
     * @param string|Bookmark $id           Can be an Bookmark object or a string ID
     *
     * @return bool                         true if Bookmark exist otherwise false
     */
    public function exist($id): bool
    {
        return (bool) $this->repo->findById($this->id($id));
    }


    /**
     * @param Bookmark $bookmark
     * @throws RuntimeException             when ID is empty or when creation failed
     */
    public function add(Bookmark $bookmark)
    {
        if (empty($bookmark->id())) {
            throw new RuntimeException("Invalid ID");
        }
        if ($this->exist($bookmark)) {
            throw new RuntimeException("ID already exist");
        }
        if (!$bookmark->ispublic()) {
            $usermanager = new Modeluser();
            $user = $usermanager->get($bookmark->user());
            if (!$user) {
                throw new RuntimeException("User: \"" . $bookmark->user() . "\" cannot be found");
            }
        }
        $bookmarkdata = new Document($bookmark->dry());
        $bookmarkdata->setId($bookmark->id());
        $success = $this->repo->store($bookmarkdata);
        if (!$success) {
            throw new RuntimeException("Error while adding Bookmark to database");
        }
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

    /**
     * @param Bookmark $bookmark            Bookmark to update
     * @return Bookmark                     updated Bookmark
     * @throws RuntimeException             if Bookmark does not exist or if an error occured at database level
     */
    public function update(Bookmark $bookmark): Bookmark
    {
        $oldbookmark = $this->get($bookmark);
        $bookmark->setuser($oldbookmark->user());
        $bookmarkdata = new Document($bookmark->dry());
        $bookmarkdata->setId($bookmark->id());
        $success = $this->repo->store($bookmarkdata);
        if (!$success) {
            throw new RuntimeException("Error while updating Bookmark to database");
        }
        return $bookmark;
    }

    /**
     * @param Bookmark|string $id           string ID or bookmark
     */
    private function id($id): string
    {
        if ($id instanceof Bookmark) {
            return $id->id();
        }
        if (is_string($id)) {
            return $id;
        }
    }
}
