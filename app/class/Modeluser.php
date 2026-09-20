<?php

namespace Wcms;

use InvalidArgumentException;
use JamesMoss\Flywheel\Document;
use Wcms\Exception\Database\Invalididexception;
use Wcms\Exception\Databaseexception;
use Wcms\Exception\Database\Notfoundexception;

class Modeluser extends Modeldb
{
    public const USER_REPO_NAME = 'user';

    public function __construct()
    {
        parent::__construct();
        $this->storeinit(self::USER_REPO_NAME);
    }


    public function logout(): User
    {
        $user = new User(['level' => User::VISITOR]);
        return $user;
    }



    /**
     * @return array<string, User>          associative array of User objects `id => User`
     *
     * @param int[] $levels                 allowed user levels
     *
     * @param string $sortby
     *
     * @param int $order
     */
    public function list($levels = [], $sortby = 'id', $order = 1): array
    {
        $users = [];
        $list = $this->repo->findAll();
        foreach ($list as $userdata) {
            $users[$userdata->id] = new User($userdata);
        }
        $users = $this->listfilter($users, $levels);
        $this->listsort($users, $sortby, $order);
        return $users;
    }

    /**
     * Check the clear password of an user
     *
     * @param User $user                    User to check
     * @param string $pass                  clear password
     *
     * @return bool                         True if password is good otherwise false
     */
    public function passwordcheck(User $user, string $pass): bool
    {
        if ($user->passwordhashed()) {
            return password_verify($pass, $user->password());
        } else {
            return $user->password() === $pass;
        }
    }

    /**
     * Add a new user in the database
     *
     * @param User $user
     *
     * @throws Databaseexception            in case of error
     */
    public function add(User $user): void
    {
        $userdata = new Document($user->dry());
        $userdata->setId($user->id());
        $this->storedoc($userdata);
    }

    /**
     * Update an user in the database
     *
     * @param User $user
     *
     * @throws Databaseexception            in case of error
     */
    public function update(User $user): void
    {
        $userdata = new Document($user->dry());
        $userdata->setId($user->id());
        $this->updatedoc($userdata);
    }


    /**
     * @param string|User $id               Can be an User object or a string ID
     *
     * @return User                         User object
     *
     * @throws Notfoundexception            If User cant be founded
     * @throws Invalididexception           If ID is invalid
     */
    public function get($id): User
    {
        if ($id instanceof User) {
            $id = $id->id();
        }
        if (!is_string($id)) {
            throw new InvalidArgumentException('input should be an User object or a string ID');
        }
        if (!$this::idcheck($id)) {
            throw new Invalididexception("invalid ID: '$id'");
        }

        $userdata = $this->repo->findById($id);
        if ($userdata === false) {
            throw new Notfoundexception("User with ID '$id'not found in the database.");
        }
        return new User($userdata);
    }

    /**
     * Check if user exist in the database or not.
     *
     * @param string|User $id               Can be an User object or a string ID
     * @return bool
     * @throws InvalidArgumentException     If $id param is not a string or an User
     */
    public function exist($id): bool
    {
        if ($id instanceof User) {
            $id = $id->id();
        }
        if (is_string($id)) {
            return boolval($this->repo->findById($id));
        } else {
            throw new InvalidArgumentException('input should be an User object or a string ID');
        }
    }

    public function delete(User $user): void
    {
        $this->repo->delete($user->id());
    }

    /**
     * Get the Users that are author of a page
     *
     * @param Page $page                    the page that have authors
     *
     * @param bool $onlyexisting            return only Users found in the database
     *                                      If set to false, basic User object are added to the list.
     *
     * @return User[]                       Associative array of User object with ID as key
     */
    public function pageauthors(Page $page, bool $onlyexisting = true): array
    {
        $users = [];
        foreach ($page->authors() as $author) {
            try {
                $user = $this->get($author);
                $users[$user->id()] = $user;
            } catch (Databaseexception $e) {
                if ($onlyexisting === false) {
                    $users[$author] = new User(['id' => $author]);
                }
            }
        }
        return $users;
    }


    /**
     * Filter an array of Urls
     *
     * @param User[] $users
     *
     * @param int[] $levels
     *
     * @return User[]
     */
    protected function listfilter(array $users, array $levels = []): array
    {
        if ($levels === []) {
            return $users;
        }

        return array_filter($users, function (User $user) use ($levels): bool {
            return in_array($user->level(), $levels);
        });
    }

    /**
     * Sort an array of Users
     *
     * @param User[] $users
     * @param string $sortby
     * @param int $order                    Can be 1 or -1
     */
    protected function listsort(array &$users, string $sortby = 'id', int $order = 1): void
    {
        $sortby = (in_array($sortby, User::SORT_BY)) ? $sortby : 'id';
        $order = ($order === 1 || $order === -1) ? $order : 1;
        uasort($users, $this->buildsorter($sortby, $order));
    }

    protected function buildsorter(string $sortby, int $order): callable
    {
        return function (User $user1, User $user2) use ($sortby, $order) {
            $result = $this->compare($user1, $user2, $sortby, $order);
            return $result;
        };
    }

    protected function compare(User $user1, User $user2, string $property = 'id', int $order = 1): int
    {
        $result = ($user1->$property() <=> $user2->$property());
        return $result * $order;
    }


    /**
     * Get all users that have their URL set, sorted by URL
     *
     * @return array<string, User[]>        key is URL, value is array of Users
     */
    public function userurls(): array
    {
        $sites = [];
        $users = $this->list();
        foreach ($users as $user) {
            if (!empty($user->url())) {
                $sites[$user->url()][] = $user;
            }
        }
        return $sites;
    }
}
