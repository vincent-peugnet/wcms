<?php

namespace Wcms;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;

class Media extends Item
{
	Protected $id;
	Protected $path;
	Protected $extension;
	Protected $type;
	Protected $size;
	Protected $date;
	Protected $width;
	Protected $height;
	Protected $length;

	const IMAGE = array('jpg', 'jpeg', 'gif', 'png');
	const SOUND = array('mp3', 'flac', 'wav', 'ogg');
	const VIDEO = array('mp4', 'mov', 'avi', 'mkv');
	const ARCHIVE = array('zip', 'rar');



// _____________________________________________________ F U N ____________________________________________________

	public function __construct(array $donnees)
	{
		$this->hydrate($donnees);
	}

	public function analyse()
	{
		$this->settype();

		$this->setdate();

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

	public function getfulldir()
	{
		return $this->path . $this->id . '.' . $this->extension;
	}

	/**
	 * Generate html code depending on media type
	 * 
	 * @return string html code
	 */
	public function getcode() : string
	{
		switch ($this->type) {
			case 'image':
				$code = '![' . $this->id . '](' . $this->getincludepath() . ')';
				break;
				
			case 'sound':
					$code = '&lt;audio controls src="' . $this->getincludepath() . '"&gt;&lt;/audio&gt;';
				break;
				
			case 'video':
					$code = '&lt;video controls=""&gt;&lt;source src="' . $this->getincludepath() . '" type="video/' . $this->extension . '"&gt;&lt;/video&gt;';
				break;

			default :
					$code = '[' . $this->id . '](' . $this->getincludepath() . ')';
				break;
			
		}
			
		return $code;

	}

	public function getsymbol()
	{
		switch ($this->type) {
			case 'image':
				$symbol = "🖼";
				break;
			
			case 'sound':
				$symbol = "🎵";
				break;
			
			case 'video':
				$symbol = "🎞";
				break;
				
			case 'document':
				$symbol = "📓";
				break;
			
			case 'archive':
				$symbol = "🗜";
				break;
					
			case 'code':
				$symbol = "📄";
				break;
							
			default :
				$symbol = "🎲";
				break;
		}
		return $symbol;
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

	public function size($display = 'binary')
	{
		if($display == 'hr') {
			return readablesize($this->size);
		} else {
			return $this->size;
		}
	}

	public function date($option = 'date')
	{
		return $this->datetransform('date', $option);
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
		if (is_string($id)) {
			$this->id = $id;
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
		if (!empty($this->extension) && isset(Model::MEDIA_EXT[$this->extension])) {
			$this->type = Model::MEDIA_EXT[$this->extension];
		} else {
			$this->type = 'other';
		}
	}

	public function setsize($size)
	{
		if (40 and is_int($size)) {
			$this->size = strip_tags(strtolower($size));
		}
	}

	public function setdate()
	{
		$timestamp = filemtime($this->getfulldir());
		$this->date = new DateTimeImmutable("@$timestamp");
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
