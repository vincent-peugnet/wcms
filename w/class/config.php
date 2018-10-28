<?php



abstract class Config
{
	protected static $host;
	protected static $dbname;
	protected static $user;
	protected static $password;
	protected static $arttable;
	protected static $domain;
	protected static $admin;
	protected static $editor;
	protected static $invite;
	protected static $read;
	protected static $color4;
	protected static $fontsize = 6;


// _______________________________________ F U N _______________________________________



	public static function hydrate(array $donnees)
	{
		foreach ($donnees as $key => $value) {
			$method = 'set' . $key;

			if (method_exists(get_called_class(), $method)) {
				self::$method($value);
			}
		}
	}

	public static function readconfig()
	{
		if (file_exists(Model::CONFIG_FILE)) {
			$current = file_get_contents(Model::CONFIG_FILE);
			$datas = json_decode($current, true);
			self::hydrate($datas);
		}
	}

	public static function createconfig(array $datas)
	{
		self::hydrate($datas);
	}


	public static function savejson()
	{
		$json = self::tojson();
		file_put_contents(self::CONFIG_FILE, $json);
	}


	public static function tojson()
	{
		$arr = get_object_vars($this);
		$json = json_encode($arr, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT);
		return $json;
	}

// ________________________________________ G E T _______________________________________

	public static function host()
	{
		return self::$host;
	}

	public static function dbname()
	{
		return self::$dbname;
	}

	public static function user()
	{
		return self::$user;
	}

	public static function password()
	{
		return self::$password;
	}

	public static function arttable()
	{
		return self::$arttable;
	}

	public static function domain()
	{
		return self::$domain;
	}

	public static function admin()
	{
		return self::$admin;
	}

	public static function editor()
	{
		return self::$editor;
	}

	public static function invite()
	{
		return self::$invite;
	}

	public static function read()
	{
		return self::$read;
	}

	public static function color4()
	{
		return self::$color4;
	}

	public static function fontsize()
	{
		return self::$fontsize;
	}



// __________________________________________ S E T ______________________________________

	public static function sethost($host)
	{
		self::$host = strip_tags($host);
	}

	public static function setdbname($dbname)
	{
		self::$dbname = strip_tags($dbname);
	}

	public static function setuser($user)
	{
		self::$user = strip_tags($user);
	}

	public static function setpassword($password)
	{
		self::$password = strip_tags($password);
	}

	public static function setarttable($arttable)
	{
		self::$arttable = strip_tags($arttable);
	}

	public static function setdomain($domain)
	{
		self::$domain = strip_tags($domain);
	}

	public static function setadmin($admin)
	{
		self::$admin = strip_tags($admin);
	}

	public static function seteditor($editor)
	{
		self::$editor = strip_tags($editor);
	}

	public static function setinvite($invite)
	{
		self::$invite = strip_tags($invite);
	}

	public static function setread($read)
	{
		self::$read = strip_tags($read);
	}

	public static function setcolor4($color4)
	{
		if(strlen($color4) <= 8) {
			self::$color4 = $color4;
		}
	}

	public static function setfontsize($fontsize)
	{
		$fontsize = intval($fontsize);
		if($fontsize > 1) {
			self::$fontsize = $fontsize;
		}
	}


}









?>