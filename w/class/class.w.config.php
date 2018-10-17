<?php



class Config
{
	private $host;
	private $dbname;
	private $user;
	private $password;
	private $arttable;
	private $domain;
	private $admin;
	private $editor;
	private $invite;
	private $read;
	private $color4;
	private $fontsize = 6;


// _______________________________________ F U N _______________________________________

	public function __construct(array $donnees)
	{
		$this->hydrate($donnees);
	}

	public function hydrate(array $donnees)
	{
		foreach ($donnees as $key => $value) {
			$method = 'set' . $key;

			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
	}

	public function tojson()
	{
		$arr = get_object_vars($this);
		$json = json_encode($arr, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT);
		return $json;
	}

// ________________________________________ G E T _______________________________________

	public function host()
	{
		return $this->host;
	}

	public function dbname()
	{
		return $this->dbname;
	}

	public function user()
	{
		return $this->user;
	}

	public function password()
	{
		return $this->password;
	}

	public function arttable()
	{
		return $this->arttable;
	}

	public function domain()
	{
		return $this->domain;
	}

	public function admin()
	{
		return $this->admin;
	}

	public function editor()
	{
		return $this->editor;
	}

	public function invite()
	{
		return $this->invite;
	}

	public function read()
	{
		return $this->read;
	}

	public function color4()
	{
		return $this->color4;
	}

	public function fontsize()
	{
		return $this->fontsize;
	}



// __________________________________________ S E T ______________________________________

	public function sethost($host)
	{
		$this->host = strip_tags($host);
	}

	public function setdbname($dbname)
	{
		$this->dbname = strip_tags($dbname);
	}

	public function setuser($user)
	{
		$this->user = strip_tags($user);
	}

	public function setpassword($password)
	{
		$this->password = strip_tags($password);
	}

	public function setarttable($arttable)
	{
		$this->arttable = strip_tags($arttable);
	}

	public function setdomain($domain)
	{
		$this->domain = strip_tags($domain);
	}

	public function setadmin($admin)
	{
		$this->admin = strip_tags($admin);
	}

	public function seteditor($editor)
	{
		$this->editor = strip_tags($editor);
	}

	public function setinvite($invite)
	{
		$this->invite = strip_tags($invite);
	}

	public function setread($read)
	{
		$this->read = strip_tags($read);
	}

	public function setcolor4($color4)
	{
		if(strlen($color4) <= 8) {
			$this->color4 = $color4;
		}
	}

	public function setfontsize($fontsize)
	{
		$fontsize = intval($fontsize);
		if($fontsize > 1) {
			$this->fontsize = $fontsize;
		}
	}


}









?>