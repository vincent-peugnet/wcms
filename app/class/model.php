<?php
class Model
{

    const CONFIG_FILE = 'config.json';
	const GLOBAL_CSS_DIR = '.' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR . 'global.css';
	const MEDIA_DIR = '.' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR;
	const TEMPLATES_DIR = '.'. DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;
	const RENDER_DIR = '.'. DIRECTORY_SEPARATOR . 'assets'. DIRECTORY_SEPARATOR . 'render' . DIRECTORY_SEPARATOR;
	const DATABASE_DIR = '.' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR;
	const MEDIA_EXTENSIONS = array('jpeg', 'jpg', 'JPG', 'png', 'gif', 'mp3', 'mp4', 'mov', 'wav', 'flac', 'pdf');
	const MEDIA_TYPES = ['image', 'video', 'sound', 'other'];

	const TEXT_ELEMENTS = ['header', 'nav', 'section', 'aside', 'footer'];



}
