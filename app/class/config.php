<?php



abstract class Config
{
	protected static $arttable = 'mystore';
	protected static $domain = '';
	protected static $color4;
	protected static $fontsize = 15;
	protected static $basepath = '';
	protected static $route404;
	protected static $existnot = 'This page does not exist yet';
	protected static $defaultbody = '%HEADER%'. PHP_EOL .PHP_EOL . '%NAV%'. PHP_EOL .PHP_EOL . '%ASIDE%'. PHP_EOL .PHP_EOL . '%MAIN%'. PHP_EOL .PHP_EOL . '%FOOTER%';
	protected static $defaultart = '';
	protected static $defaultfavicon = '';
	protected static $showeditmenu = true;
	protected static $editsymbol = 'pen';	
	protected static $analytics = '';	
	protected static $externallinkblank = true;
	protected static $internallinkblank = false;
	protected static $reccursiverender = true;
	protected static $defaultprivacy = 0;
	protected static $homepage = 'default';
	protected static $homeredirect = null;


// _______________________________________ F U N _______________________________________



	public static function hydrate(array $datas)
	{
		foreach ($datas as $key => $value) {
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
			return true;
		} else {
			return false;
		}
	}

	public static function createconfig(array $datas)
	{
		self::hydrate($datas);
	}


	public static function savejson()
	{
		$json = self::tojson();
		return file_put_contents(Model::CONFIG_FILE, $json);
	}


	public static function tojson()
	{
		$arr = get_class_vars(__class__);
		$json = json_encode($arr, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT);
		return $json;
	}

	public static function checkbasepath()
	{
		$path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . self::basepath() . DIRECTORY_SEPARATOR .  Model::CONFIG_FILE;
		return (file_exists($path));
	}

	/**
	 * Calculate Domain name
	 */
    public static function getdomain()
    {
        self::$domain = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
	}

	/**
	 * Verify Domain name
	 */
	public static function checkdomain()
	{
		return (self::$domain === $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']);
	}

// ________________________________________ G E T _______________________________________

	public static function arttable()
	{
		return self::$arttable;
	}

	public static function domain()
	{
		return self::$domain;
	}

	public static function color4()
	{
		return self::$color4;
	}

	public static function fontsize()
	{
		return self::$fontsize;
	}

	public static function basepath()
	{
		return self::$basepath;
	}

	public static function route404()
	{
		return self::$route404;
	}

	public static function existnot()
	{
		return self::$existnot;
	}

	public static function defaultbody()
	{
		return self::$defaultbody;
	}

	public static function defaultart()
	{
		return self::$defaultart;
	}

	public static function defaultfavicon()
	{
		return self::$defaultfavicon;
	}

	public static function showeditmenu()
	{
		return self::$showeditmenu;
	}

	public static function editsymbol()
	{
		return self::$editsymbol;
	}

	public static function analytics()
	{
		return self::$analytics;
	}

	public static function externallinkblank()
	{
		return self::$externallinkblank;
	}

	public static function internallinkblank()
	{
		return self::$internallinkblank;
	}

	public static function reccursiverender()
	{
		return self::$reccursiverender;
	}

	public static function defaultprivacy()
	{
		return self::$defaultprivacy;
	}

	public static function homepage()
	{
		return self::$homepage;
	}

	public static function homeredirect()
	{
		return self::$homeredirect;
	}



// __________________________________________ S E T ______________________________________

	public static function setarttable($arttable)
	{
		self::$arttable = strip_tags($arttable);
	}

	public static function setdomain($domain)
	{
		self::$domain = strip_tags(strtolower($domain));
	}

	public static function setcolor4($color4)
	{
		if (strlen($color4) <= 8) {
			self::$color4 = $color4;
		}
	}

	public static function setfontsize($fontsize)
	{
		$fontsize = intval($fontsize);
		if ($fontsize > 1) {
			self::$fontsize = $fontsize;
		}
	}

	public static function setbasepath($basepath)
	{
		self::$basepath = strip_tags($basepath);
	}

	public static function setroute404($id)
	{
		if(is_string($id)) {
			self::$route404 = idclean($id);
		}
	}

	public static function setexistnot($description)
	{
		if(is_string($description)) {
			self::$existnot = strip_tags($description);
		}
	}

	public static function setdefaultbody($defaultbody)
	{
		if(is_string($defaultbody)) {
			self::$defaultbody = $defaultbody;
		}
	}

	public static function setdefaultfavicon($defaultfavicon)
	{
		if(is_string($defaultfavicon)) {
			self::$defaultfavicon = $defaultfavicon;
		}
	}

	public static function setdefaultart($defaultart)
	{
		if(is_string($defaultart)) {
			self::$defaultart = idclean($defaultart);
		}
	}

	public static function setshoweditmenu($showeditmenu)
	{
		if(is_bool($showeditmenu)) {
			self::$showeditmenu = $showeditmenu;
		} elseif (is_string($showeditmenu)) {
			if($showeditmenu === 'on') {
				self::$showeditmenu = true;
			}
		}
	}

	public static function seteditsymbol($editsymbol)
	{
		if(is_string($editsymbol))
		{
			self::$editsymbol = $editsymbol;
		}
	}

	public static function setanalytics($analytics)
	{
		if(is_string($analytics) && strlen($analytics) < 25) {
			self::$analytics = $analytics;
		}
	}
	
	public static function setexternallinkblank($externallinkblank)
	{
		self::$externallinkblank = boolval($externallinkblank);
	}

	public static function setinternallinkblank($internallinkblank)
	{
		self::$internallinkblank = boolval($internallinkblank);
	}

	public static function setreccursiverender($reccursiverender)
	{
		self::$reccursiverender = boolval($reccursiverender);
	}

	public static function setdefaultprivacy($defaultprivacy)
	{
		$defaultprivacy = intval($defaultprivacy);
		if($defaultprivacy >= 0 && $defaultprivacy <= 2) {
			self::$defaultprivacy = $defaultprivacy;
		}
	}

	public static function sethomepage($homepage)
	{
		if(in_array($homepage, Model::HOMEPAGE)) {
			self::$homepage = $homepage;
		}
	}

	public static function sethomeredirect($homeredirect)
	{
		if(is_string($homeredirect) && strlen($homeredirect) > 0) {
			self::$homeredirect = idclean($homeredirect);
		} else {
			self::$homeredirect = null;
		}
	}
	



}









?>