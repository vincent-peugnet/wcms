<?php

namespace Wcms;

use JamesMoss\Flywheel\Document;

class Modeluser extends Modeldb
{
    const ADMIN = 10;
    const SUPEREDITOR = 4;
    const EDITOR = 3;
    const INVITE = 2;
    const READ = 1;
    const FREE = 0;

    const USER_REPO_NAME = 'user';

    public function __construct()
    {
        parent::__construct();
        $this->storeinit(self::USER_REPO_NAME);
    }

    /**
     * Write session cookie according to users datas and define the current authtoken being used
     * 
     * @param User $user Current user to keep in session
     */
    public function writesession(User $user)
    {
        $_SESSION['user' . Config::basepath()]['level'] = $user->level();
        $_SESSION['user' . Config::basepath()]['id'] = $user->id();
        $_SESSION['user' . Config::basepath()]['columns'] = $user->columns();
    }

    public function readsession()
    {
        $userdatas = [];
        if (array_key_exists('user' . Config::basepath(), $_SESSION) && isset($_SESSION['user' . Config::basepath()]['id'])) {
            $userdatas = $_SESSION['user' . Config::basepath()];
            $user = new User($userdatas);
            $user = $this->get($user);
            return $user;
        }
        
        if(isset($_COOKIE['authtoken']) && strpos($_COOKIE['authtoken'], ':')) {
            list($cookietoken, $cookiemac) = explode(':', $_COOKIE['authtoken']);
            $authtokenmanager = new Modelauthtoken();
            $dbtoken = $authtokenmanager->getbytoken($cookietoken);

            if ($dbtoken !== false) {
                if(hash_equals($cookiemac, secrethash($dbtoken->getId()))) {
                    $user = $this->get($dbtoken->user);
                    if ($user !== false) {
                        $this->writesession($user, $_COOKIE['authtoken']);
                    }
                    return $user;
                }

            }
        }

        return new User(['id' => '', 'level' => 0]);

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


    public function pagelistbyid(array $idlist = [])
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
     * @param string $pass password clear
     * 
     * @return mixed User or false
     */
    public function passwordcheck(string $pass)
    {
        $userdatalist = $this->getlister();
        foreach ($userdatalist as $user) {
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
     * Return information if the password is already used or not
     * 
     * @param string $pass password clear
     * 
     * @return bool password exist or not
     */
    public function passwordexist(string $pass) : bool
    {
        if ($this->passwordcheck($pass) !== false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param User $user
     * 
     * @return bool depending on success
     */
    public function add(User $user) : bool
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






?>