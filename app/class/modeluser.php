<?php

class Modeluser extends Modeldb
{
    const ADMIN = 10;
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
        $_SESSION['user' . Config::basepath()] = ['level' => $user->level(), 'id' => $user->id()];
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

    public function login($pass)
    {
        $passlevel = $this->passlevel($pass);
        if($passlevel != false) {
            $user = new User($passlevel);
            return $user;
        } else {
            return false;
        }
    }

    public function passlevel($pass)
    {
        $userdatalist = $this->repo->query()
		->where('password', '==', $pass)
        ->execute();
        
        if($userdatalist->total() === 1) {
            return $userdatalist[0];
        } else {
            return 0;
        }
    }

    public function invitetest($pass)
    {
        $invitepasslist = [];
        if (in_array($pass, $invitepasslist)) {
            return true;
        } else {
            return false;
        }
    }

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

    public function getlisterbylevel(int $level)
    {
        $userdatalist = $this->repo->query()
		->where('level', '==', $level)
        ->execute();
        
        $userlist = [];
        foreach ($userdatalist as $user) {
            $userlist[] = $user->id;
        }

        return $userlist;
    }

    public function passwordexist(string $pass)
    {
        $userdatalist = $this->repo->query()
		->where('password', '==', $pass)
		->execute();

        if($userdatalist->total() === 0) {
            return false;
        } else {
            return true;
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