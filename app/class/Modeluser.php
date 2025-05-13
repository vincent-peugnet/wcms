<?php

namespace Wcms;

use InvalidArgumentException;
use JamesMoss\Flywheel\Document;
use Wcms\Exception\Databaseexception;
use Wcms\Exception\Database\Notfoundexception;

class Modeluser extends Modeldb
{
    public const ADMIN = 10;
    public const SUPEREDITOR = 4;
    public const EDITOR = 3;
    public const INVITE = 2;
    public const READ = 1;
    public const FREE = 0;

    public const USER_REPO_NAME = 'user';

    public function __construct()
    {
        parent::__construct();
        $this->storeinit(self::USER_REPO_NAME);
    }


    public function logout()
    {
        $user = new User(['level' => self::FREE]);
        return $user;
    }



    /**
     * @return User[] associative array of User objects `id => User`
     */
    public function getlister()
    {
        $userlist = [];
        $list = $this->repo->findAll();
        foreach ($list as $userdata) {
            $userlist[$userdata->id] = new User($userdata);
        }
        return $userlist;
    }

    /**
     * @param string[] $idlist      List of user ID
     * @return User[]               List of User
     */
    public function userlistbyid(array $idlist = []): array
    {
        $userdatalist = $this->repo->query()
            ->where('__id', 'IN', $idlist)
            ->execute();

        $userlist = [];
        foreach ($userdatalist as $id => $userdata) {
            $userlist[$id] = new User($userdata);
        }
        return $userlist;
    }

    public function admincount()
    {
        $userdatalist = $this->repo->query()
            ->where('level', '==', 10)
            ->execute();

        return $userdatalist->total();
    }

    /**
     * @param int $level                    Level of user (see consts)
     * @param string $comp                  Comparaison operator
     * @return User[]                       List of User object using ID as key
     */
    public function getlisterbylevel(int $level, string $comp = '==', bool $orderbylevel = false): array
    {
        $userdatalist = $this->repo->query()
            ->where('level', $comp, $level)
            ->orderBy($orderbylevel ? 'level ASC' : 'id ASC')
            ->execute();

        $userlist = [];
        foreach ($userdatalist as $user) {
            $userlist[$user->id] = new User($user);
        }

        return $userlist;
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
        if (!$this->storedoc($userdata)) {
            throw new Databaseexception("Database error while editing user.");
        }
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
        if (!$this->updatedoc($userdata)) {
            throw new Databaseexception("Database error while editing user.");
        }
    }


    /**
     * @param string|User $id               Can be an User object or a string ID
     *
     * @return User                         User object
     *
     * @throws Notfoundexception            If User cant be founded
     * @throws InvalidArgumentException     If $id param is not a string or an User
     */
    public function get($id): User
    {
        if ($id instanceof User) {
            $id = $id->id();
        }
        if (is_string($id)) {
            $userdata = $this->repo->findById($id);
            if ($userdata !== false) {
                return new User($userdata);
            } else {
                throw new Notfoundexception("User with ID $id not found in the database.");
            }
        } else {
            throw new InvalidArgumentException('input should be an User object or a string ID');
        }
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

    public function delete(User $user)
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
            } catch (Notfoundexception) {
                if ($onlyexisting === false) {
                    $users[$author] = new User(['id' => $author]);
                }
            }
        }
        return $users;
    }
}
