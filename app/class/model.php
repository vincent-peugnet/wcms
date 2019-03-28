<?php
abstract class Model
{

    const CONFIG_FILE = 'config.json';
	const CSS_DIR = 'assets' . DIRECTORY_SEPARATOR .'css' . DIRECTORY_SEPARATOR;
	const JS_DIR = 'assets' . DIRECTORY_SEPARATOR .'js' . DIRECTORY_SEPARATOR;
	const FONT_DIR = 'fonts' . DIRECTORY_SEPARATOR;
	const MEDIA_DIR = 'media' . DIRECTORY_SEPARATOR;
	const FAVICON_DIR = 'media' . DIRECTORY_SEPARATOR . 'favicon' . DIRECTORY_SEPARATOR;
	const THUMBNAIL_DIR = 'media' . DIRECTORY_SEPARATOR . 'thumbnail' . DIRECTORY_SEPARATOR;
	const TEMPLATES_DIR = '.'. DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;
	const RENDER_DIR = 'assets'. DIRECTORY_SEPARATOR . 'render' . DIRECTORY_SEPARATOR;
	const GLOBAL_DIR = 'assets'. DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR;
	const DATABASE_DIR = '.' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR;
	
	const MEDIA_EXTENSIONS = array('jpeg', 'jpg', 'JPG', 'png', 'gif', 'mp3', 'mp4', 'mov', 'wav', 'flac', 'pdf');
	const MEDIA_TYPES = ['image', 'video', 'sound', 'other'];

	const COLUMNS = ['title', 'description', 'tag', 'date', 'datemodif', 'datecreation', 'secure', 'linkfrom', 'linkto', 'visitcount', 'affcount', 'editcount'];

	const TEXT_ELEMENTS = ['header', 'nav', 'main', 'aside', 'footer'];
	const EDIT_SYMBOLS = ['pen', 'tool', 'none'];

	const MAX_ID_LENGTH = 64;
	const PASSWORD_HASH = true;
	const PASSWORD_MIN_LENGTH = 4;
	const PASSWORD_MAX_LENGTH = 32;

	/** RENDER OPTIONS	 */
	// add class in html element indicating from witch page the content come.
	const RENDER_CLASS_ORIGIN = false;
	// render empty CONTENT element as empty html element, if set to false, render html comment
	const RENDER_EMPTY_ELEMENT = false; 


	/** CONFIG OPTIONS */
	const HOMEPAGE = ['default', 'search', 'redirect'];

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

	public static function jspath() 
	{
		return self::dirtopath(Model::JS_DIR);
	}

	public static function mediapath()
	{
		return self::dirtopath(Model::MEDIA_DIR);
	}

	public static function faviconpath()
	{
		return self::dirtopath(Model::FAVICON_DIR);
	}

	public static function thumbnailpath()
	{
		return self::dirtopath(Model::THUMBNAIL_DIR);
	}

	public static function fontpath()
	{
		return self::dirtopath(Model::FONT_DIR);
	}

}
