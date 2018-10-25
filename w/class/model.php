<?php
class Model extends Application
{

    const CONFIG_FILE = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'w.config.json';
	const GLOBAL_CSS_DIR = '.' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR . 'global.css';
	const MEDIA_DIR = '.' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR;
	const TEMPLATES_DIR = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'templates';
	const MEDIA_EXTENSIONS = array('jpeg', 'jpg', 'JPG', 'png', 'gif', 'mp3', 'mp4', 'mov', 'wav', 'flac', 'pdf');
	const MEDIA_TYPES = ['image', 'video', 'sound', 'other'];

	public function __construct() {
		parent::__construct();
	}


}
