<?php


class Modeluser extends Model
{
    const ADMIN = 10;
	const EDITOR = 3;
	const INVITE = 2;
	const READ = 1;
    const FREE = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function writesession(User $user)
    {
        $_SESSION['user'] = (array) $user;
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
        if (strip_tags($pass) == $this->config->admin()) {
			return $level = self::ADMIN;
		} elseif (strip_tags($pass) == $this->config->read()) {
			return $level = self::READ;
		} elseif (strip_tags($pass) == $this->config->editor()) {
			return $level = self::EDITOR;
		} elseif (strip_tags($pass) == $this->config->invite()) {
			return $level = self::INVITE;
        }
    }

	public function logout()
	{
        $user = new User(['level' => 0]);
        return $user;
	}
}






?>