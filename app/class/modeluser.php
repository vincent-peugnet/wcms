<?php


class Modeluser extends Model
{
    const ADMIN = 10;
	const EDITOR = 3;
	const INVITE = 2;
	const READ = 1;
    const FREE = 0;


    public function writesession(User $user)
    {
        $_SESSION['user'] = ['level' => $user->level()];
    }

    public function readsession()
    {
        $userdatas = [];
        if(array_key_exists('user', $_SESSION)) {
            $userdatas = $_SESSION['user'];
        }
        $user = new User($userdatas);
        return $user;
    }
    
    public function login($pass)
	{
        $user = new User(['level' => $this->passlevel($pass)]);
        return $user;
    }
    
    public function passlevel($pass)
    {
        if (strip_tags($pass) == Config::admin()) {
			return $level = self::ADMIN;
		} elseif (strip_tags($pass) == Config::read()) {
			return $level = self::READ;
		} elseif (strip_tags($pass) == Config::editor()) {
			return $level = self::EDITOR;
		} elseif (strip_tags($pass) == Config::invite()) {
			return $level = self::INVITE;
        } else {
			return $level = self::FREE;
        }
    }

	public function logout()
	{
        $user = new User(['level' => self::FREE]);
        return $user;
	}
}






?>