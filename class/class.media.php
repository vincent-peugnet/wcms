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





}

?>