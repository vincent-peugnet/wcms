<?php

namespace Wcms;

use JamesMoss\Flywheel\Document;
use RuntimeException;

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

    public function getlisterbylevel(int $level, $comp = '==')
    {
        $userdatalist = $this->repo->query()
            ->where('level', $comp, $level)
            ->execute();

        $userlist = [];
        foreach ($userdatalist as $user) {
            $userlist[] = $user->id;
        }

        return $userlist;
    }

    /**
     * Check if the password is used, and return by who
     *
     * @param string $userid user ID
     * @param string $pass password clear
     *
     * @return User|bool User or false
     */
    public function passwordcheck(string $userid, string $pass)
    {
        $user = $this->get($userid);
        if ($user !== false) {
            if ($user->passwordhashed()) {
                if (password_verify($pass, $user->password())) {
                    return $user;
                }
            } else {
                if ($user->password() === $pass) {
                    return $user;
                }
            }
        }
        return false;
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
     * @param string|User $id Can be an User object or a string ID
     *
     * @return User|false User object or false in case of error
     */
    public function get($id)
    {
        if ($id instanceof User) {
            $id = $id->id();
        }
        if (is_string($id)) {
            $userdata = $this->repo->findById($id);
            if ($userdata !== false) {
                return new User($userdata);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function delete(User $user)
    {
        $this->repo->delete($user->id());
    }
}
