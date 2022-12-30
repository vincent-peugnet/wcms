<?php

namespace Wcms;

use InvalidArgumentException;
use JamesMoss\Flywheel\Document;
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

    /**
     * Write session cookie according to users datas
     *
     * @param User $user Current user to keep in session
     */
    public function writesession(User $user)
    {
        $_SESSION['user' . Config::basepath()]['level'] = $user->level();
        $_SESSION['user' . Config::basepath()]['id'] = $user->id();
        $_SESSION['user' . Config::basepath()]['columns'] = $user->columns();
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
     * @param User $user
     *
     * @return bool depending on success
     */
    public function add(User $user): bool
    {
        $userdata = new Document($user->dry());
        $userdata->setId($user->id());
        return $this->repo->store($userdata);
    }


    /**
     * @param string|User $id               Can be an User object or a string ID
     *
     * @return User                         User object or false in case of error
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
}
