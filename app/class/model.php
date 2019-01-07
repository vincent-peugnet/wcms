<?php
abstract class Model
{

    const CONFIG_FILE = 'config.json';
	const CSS_DIR = 'assets' . DIRECTORY_SEPARATOR .'css' . DIRECTORY_SEPARATOR;
	const FONT_DIR = 'fonts' . DIRECTORY_SEPARATOR;
	const MEDIA_DIR = 'media' . DIRECTORY_SEPARATOR;
	const FAVICON_DIR = 'media' . DIRECTORY_SEPARATOR . 'favicon' . DIRECTORY_SEPARATOR;
	const TEMPLATES_DIR = '.'. DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;
	const RENDER_DIR = 'assets'. DIRECTORY_SEPARATOR . 'render' . DIRECTORY_SEPARATOR;
	const GLOBAL_DIR = 'assets'. DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR;
	const DATABASE_DIR = '.' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR;
	
	const MEDIA_EXTENSIONS = array('jpeg', 'jpg', 'JPG', 'png', 'gif', 'mp3', 'mp4', 'mov', 'wav', 'flac', 'pdf');
	const MEDIA_TYPES = ['image', 'video', 'sound', 'other'];

	const TEXT_ELEMENTS = ['header', 'nav', 'main', 'aside', 'footer'];
	const EDIT_SYMBOLS = ['pen', 'tool', 'none'];

	const MAX_ID_LENGTH = 64;

	/** RENDER OPTIONS	 */
	const RENDER_CLASS_ORIGIN = false;
	const RENDER_EMPTY_ELEMENT = false;

	public static function dirtopath($dir)
	{
		$basepath = '';
		if(!empty(Config::basepath())) {
			$basepath = Config::basepath() . '/'  ;
		}
		$dir = str_replace('\\', '/', $dir);
		return '/' . $basepath . $dir;
	}

	public static function renderpath()
	{
		return self::dirtopath(Model::RENDER_DIR);
	}

	public static function globalpath()
	{
		return self::dirtopath(Model::GLOBAL_DIR);
	}

	public static function csspath() 
	{
		return self::dirtopath(Model::CSS_DIR);
	}

	public static function mediapath()
	{
		return self::dirtopath(Model::MEDIA_DIR);
	}

	public static function faviconpath()
	{
		return self::dirtopath(Model::FAVICON_DIR);
	}

	public static function fontpath()
	{
		return self::dirtopath(Model::FONT_DIR);
	}

}
