<?php

namespace Wcms;

use InvalidArgumentException;
use JamesMoss\Flywheel\Document;
use RuntimeException;
use Wcms\Exception\Database\Invalididexception;
use Wcms\Exception\Databaseexception;
use Wcms\Exception\Database\Notfoundexception;

class Modelbookmark extends Modeldb
{
    public const BOOKMARK_REPO_NAME = 'bookmark';

    public const BOOKMARK_ICONS = [
        '⭐️', '🖤', '🏴', '👍', '📌', '💡', '🌘', '☂️', '✈️', '🚲', '💾', '💿', '💎', '🎞', ' ⚒', '💊', '📜',
        '📒', '🔓', '🌡', '☎️', '✝️', '☢️', '✅', '🌐', '🌍', '✳️', '🏴', '😎', '👻', '💩', '⚡️', '🍸', '🔍', '📦',
        '🍴', '⚽️', '🏭', '🚀', '⚓️', '🔒'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->storeinit(self::BOOKMARK_REPO_NAME);
    }

    /**
     * Try to create the default bookmarks if their IDs are free
     *
     * @throws RuntimeException             if an error occured
     */
    public function defaults(): void
    {
        foreach ($this->defaultbookmarks() as $bookmark) {
            if (!$this->exist($bookmark->id())) {
                $this->add($bookmark);
            }
        }
    }

    /**
     * @return Bookmark[]                   associative array of Bookmark objects `id => Bookmark`
     */
    public function list(): array
    {
        $bookmarks = [];
        $list = $this->repo->findAll();
        foreach ($list as $bookmarkdata) {
            $bookmarks[$bookmarkdata->id] = new Bookmark($bookmarkdata);
        }
        return $bookmarks;
    }



    /**
     * @param string $user                  user field in Bookmark
     * @param string $comp                  comparaison operator
     * @return Bookmark[]                   array of Bookmarks objects with IDs as key
     */
    public function listbyuserid(string $user, $comp = '==='): array
    {
        $bookmarkdatas = $this->repo->query()
            ->where('user', $comp, $user)
            ->execute();

        $bookmarks = [];
        foreach ($bookmarkdatas as $bookmarkdata) {
            $bookmarks[$bookmarkdata->id] = new Bookmark($bookmarkdata);
        }
        return $bookmarks;
    }



    /**
     * Return all public bookmarks
     *
     * @return Bookmark[]                   array of Bookmarks objects with IDs as key
     */
    public function listpublic(): array
    {
        return $this->listbyuserid("");
    }



    /**
     * Return all personal bookmarks from a specific user
     *
     * @param User $user                    User owning bookmarks
     * @return Bookmark[]                   array of Bookmarks objects with IDs as key
     */
    public function listbyuser(User $user): array
    {
        return $this->listbyuserid($user->id());
    }



    /**
     * @param string|Bookmark $id           Can be an User object or a string ID
     *
     * @return Bookmark                     Bookmark object or false in case of error
     *
     * @throws Invalididexception           If ID is not valid
     * @throws Notfoundexception            If Bookmark cant be found
     * @throws RuntimeException             If Bookmark cannot be build beccause of invalid datas
     */
    public function get($id): Bookmark
    {
        $id = $this->id($id);
        if (!$this->idcheck($id)) {
            throw new Invalididexception("invalid ID: '$id'");
        }
        $bookmarkdata = $this->repo->findById($id);
        if ($bookmarkdata === false) {
            throw new Notfoundexception("Could not find Bookmark with the following ID: '$id'");
        }
        return new Bookmark($bookmarkdata);
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
     * @throws Databaseexception            When ID is invalid, already exist or creation failed
     */
    public function add(Bookmark $bookmark): void
    {
        if (empty($bookmark->id())) {
            $id = $bookmark->id();
            throw new Databaseexception("Invalid ID : $id");
        }
        if ($this->exist($bookmark)) {
            throw new Databaseexception("ID already exist");
        }
        $bookmarkdata = new Document($bookmark->dry());
        $bookmarkdata->setId($bookmark->id());
        $this->storedoc($bookmarkdata);
    }

    /**
     * Delete bookmark and it's associated RSS xml file if published
     *
     * @param Bookmark $bookmark            Bookmark to be deleted
     * @throws RuntimeException             when removing bookmark or deleting RSS failed
     */
    public function delete(Bookmark $bookmark): void
    {
        $success = $this->repo->delete($bookmark->id());
        if (!$success) {
            throw new RuntimeException("Bookmark \"" . $bookmark->id() . "\" could not be deleted ");
        }
        if ($bookmark->ispublished()) {
            if (!unlink(Servicerss::atomfile($bookmark->id()))) {
                throw new RuntimeException("Bookmark's RSS feed \"" . $bookmark->id() . "\" could not be deleted ");
            }
        }
    }

    /**
     * @param Bookmark $bookmark            Bookmark to update
     * @throws RuntimeException             if Bookmark does not exist or if an error occured at database level
     */
    public function update(Bookmark $bookmark): void
    {
        $oldbookmark = $this->get($bookmark);
        $bookmark->setuser($oldbookmark->user());

        if (!empty($bookmark->ref())) {
            $pagemanager = new Modelpage(Config::pagetable());
            $pagemanager->get($bookmark->ref());
        }

        $bookmarkdata = new Document($bookmark->dry());
        $bookmarkdata->setId($bookmark->id());
        $this->updatedoc($bookmarkdata);
    }

    /**
     * Create a bookmark that filter pages where the given user is an author.
     *
     * @param User $user                    The concerned user
     *
     * @throws RuntimeException             If the process failed
     */
    public function addauthorbookmark(User $user): void
    {
        $userbookmark = new Bookmark();
        $uid = $user->id();
        $userbookmark->init(
            "$uid-is-author",
            "?authorfilter[0]=$uid&submit=filter",
            '👤',
            "$uid's pages",
            "Pages where $uid is listed as an author",
        );
        $userbookmark->setuser($user->id());
        $this->add($userbookmark);
    }

    /**
     * @param Bookmark|string $id           string ID or bookmark
     *
     * @throws InvalidArgumentException     if $id is not a string or a Bookmark
     */
    private function id($id): string
    {
        if ($id instanceof Bookmark) {
            return $id->id();
        } elseif (is_string($id)) {
            return $id;
        } else {
            throw new InvalidArgumentException("ID input should be a string or an instance of Bookmark");
        }
    }


    /**
     * A set that can be used as default public bookmarks
     *
     * @return Bookmark[]
     */
    protected function defaultbookmarks(): array
    {
        $lastedited = new Opt(['sortby' => 'datemodif', 'limit' => 5, 'order' => -1]);
        $lasteditedbookmark = new Bookmark();
        $lasteditedbookmark->init(
            'last5edited',
            $lastedited->getaddress(),
            '🕒',
            'Last 5 edited',
            'Get the 5 last edited pages of the database'
        );

        $lastcreated = new Opt(['sortby' => 'datecreation', 'limit' => 10, 'order' => -1]);
        $lastcreatedbookmark = new Bookmark();
        $lastcreatedbookmark->init(
            'last10created',
            $lastcreated->getaddress(),
            '🖍️',
            'Last 10 created',
            'Get the 10 last created pages of the database'
        );

        $emptytag = new Opt(['tagcompare' => 'EMPTY']);
        $emptytagbookmark = new Bookmark();
        $emptytagbookmark->init(
            'notags',
            $emptytag->getaddress(),
            '🏷️',
            'No tags',
            'Pages that does\'nt have any tag'
        );

        $all = new Opt();
        $allbookmark = new Bookmark();
        $allbookmark->init('all', $all->getaddress(), '⚓', 'All', 'Show all pages');

        return [
            $lasteditedbookmark,
            $lastcreatedbookmark,
            $emptytagbookmark,
            $allbookmark,
        ];
    }
}
