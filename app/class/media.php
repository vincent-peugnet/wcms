<?php

class Media
{
	private $id;
	private $path;
	private $extension;
	private $type;
	private $size;
	private $width;
	private $height;
	private $length;

	const IMAGE = array('jpg', 'jpeg', 'gif', 'png');
	const SOUND = array('mp3', 'flac');
	const VIDEO = array('mp4', 'mov', 'avi');



// _____________________________________________________ F U N ____________________________________________________

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


	public function analyse()
	{
		$this->settype();

		$filepath = $this->path . $this->id . '.' . $this->extension;

		$this->size = filesize($filepath);

		if ($this->type == 'image') {
			list($width, $height, $type, $attr) = getimagesize($filepath);
			$this->width = $width;
			$this->height = $height;
		}


	}


	public function getfullpath()
	{
		if(!empty(Config::basepath())) {
			$base = '/' . Config::basepath();
		} else {
			$base = '';
		}
		$fullpath = $base . '/'. $this->path() . $this->id() . '.' . $this->extension();
		$fullpath = str_replace('\\', '/', $fullpath);
		return $fullpath;
	}

	public function getincludepath()
	{
		$includepath = $this->path() . $this->id() . '.' . $this->extension();
		$includepath = str_replace('\\', '/', $includepath);
		$includepath = substr($includepath, 6);
		return $includepath;
	}



// _________________________________________________ G E T ____________________________________________________

	public function id()
	{
		return $this->id;
	}

	public function path()
	{
		return $this->path;
	}

	public function extension()
	{
		return $this->extension;
	}

	public function type()
	{
		return $this->type;
	}

	public function size()
	{
		return $this->size;
	}

	public function width()
	{
		return $this->width;
	}

	public function height()
	{
		return $this->height;
	}

	public function length()
	{
		return $this->length;
	}

// ___________________________________________________ S E T __________________________________________________

	public function setid($id)
	{
		if (strlen($id) < 40 and is_string($id)) {
			$this->id = strip_tags(strtolower($id));
		}
	}

	public function setpath($path)
	{
		if (strlen($path) < 40 and is_string($path)) {
			$this->path = strip_tags(strtolower($path));
		}
	}

	public function setextension($extension)
	{
		if (strlen($extension) < 7 and is_string($extension)) {
			$this->extension = strip_tags(strtolower($extension));
		}
	}

	public function settype()
	{
		if (isset($this->extension)) {
			if (in_array($this->extension, $this::IMAGE)) {
				$this->type = "image";
			} elseif (in_array($this->extension, $this::SOUND)) {
				$this->type = "sound";
			} elseif (in_array($this->extension, $this::VIDEO)) {
				$this->type = "video";
			} else {
				$this->type = "other";
			}
		}
	}

	public function setsize($size)
	{
		if (40 and is_int($size)) {
			$this->size = strip_tags(strtolower($size));
		}
	}

	public function setwidth($width)
	{
		if (is_int($width)) {
			$this->width = strip_tags(strtolower($width));
		}
	}

	public function setheight($height)
	{
		if (is_int($height)) {
			$this->height = strip_tags(strtolower($height));
		}
	}

	public function setlength($length)
	{
		if ($this->type == 'sound') {
			$this->length = $length;
		}
	}






}

?>