<?php

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

    public function writesession(User $user)
    {
        $_SESSION['user' . Config::basepath()] = ['level' => $user->level(), 'id' => $user->id(), 'columns' =>$user->columns()];
    }

    public function writecookie(User $user)
    {
        $cookiehash = 
        $cookie = ['level' => $user->level(), 'id' => $user->id()];
        setcookie('user ' . Config::basepath(), $cookie, time() + $user->cookie()*24*3600, null, null, false, true);
    }

    public function readsession()
    {
        $userdatas = [];
        if (array_key_exists('user' . Config::basepath(), $_SESSION) && isset($_SESSION['user' . Config::basepath()]['id'])) {
            $userdatas = $_SESSION['user' . Config::basepath()];
            $user = new User($userdatas);
            return $user;
        } else {
            return new User(['id' => '', 'level' => 0]);
        }
    }

    // public function invitetest($pass)
    // {
    //     $invitepasslist = [];
    //     if (in_array($pass, $invitepasslist)) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    public function logout()
    {
        $user = new User(['level' => self::FREE]);
        return $user;
    }



    public function getlister()
    {
        $userlist = [];
        $list = $this->repo->findAll();
        foreach ($list as $userdata) {
            $userlist[$userdata->id] = new User($userdata);
        }
        return $userlist;
    }


    public function getlisterid(array $idlist = [])
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

    public function add(User $user)
    {
        $userdata = new \JamesMoss\Flywheel\Document($user->dry());
        $userdata->setId($user->id());
        $this->repo->store($userdata);
    }


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