<?php
class Model
{

    const CONFIG_FILE = 'config.json';
	const CSS_DIR = 'assets' . DIRECTORY_SEPARATOR .'css' . DIRECTORY_SEPARATOR;
	const FONT_DIR = 'fonts' . DIRECTORY_SEPARATOR;
	const MEDIA_DIR = '.' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR;
	const TEMPLATES_DIR = '.'. DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;
	const RENDER_DIR = 'assets'. DIRECTORY_SEPARATOR . 'render' . DIRECTORY_SEPARATOR;
	const DATABASE_DIR = '.' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR;
	const MEDIA_EXTENSIONS = array('jpeg', 'jpg', 'JPG', 'png', 'gif', 'mp3', 'mp4', 'mov', 'wav', 'flac', 'pdf');
	const MEDIA_TYPES = ['image', 'video', 'sound', 'other'];

	const TEXT_ELEMENTS = ['header', 'nav', 'section', 'aside', 'footer'];

	public static function renderpath()
	{
		$basepath = '';
		if(!empty(Config::basepath())) {
			$basepath = Config::basepath() . DIRECTORY_SEPARATOR ;
		}
		return DIRECTORY_SEPARATOR . $basepath . Model::RENDER_DIR;
	}

	public static function csspath() 
	{
		$basepath = '';
		if(!empty(Config::basepath())) {
			$basepath = Config::basepath() . DIRECTORY_SEPARATOR ;
		}
		return DIRECTORY_SEPARATOR . $basepath . Model::CSS_DIR;
	}

	public function fontpath()
	{
		$basepath = '';
		if(!empty(Config::basepath())) {
			$basepath = Config::basepath() . DIRECTORY_SEPARATOR ;
		}
		return $basepath . Model::FONT_DIR;
	}



}
