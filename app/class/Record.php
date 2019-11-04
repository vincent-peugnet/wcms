<?php

namespace Wcms;

class Record
{
	private $id;
	private $path;
	private $extension;
	private $size;
	private $datetime;
	private $number;



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

	public function size()
	{
		return $this->size;
	}

	public function datetime()
	{
		return $this->datetime;
	}

	public function number()
	{
		return $this->number;
	}

// ___________________________________________________ S E T __________________________________________________

	public function setid($id)
	{
		if (strlen($id) < 100 and is_string($id)) {
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

	public function setdatetime($datetime)
	{
		if (is_int($datetime)) {
			$this->datetime = strip_tags(strtolower($datetime));
		}
	}

	public function setnumber($number)
	{
		if (is_int($number)) {
			$this->number = strip_tags(strtolower($number));
		}
	}





}

?>