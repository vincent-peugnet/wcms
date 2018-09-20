<?php
class Opt
{
	private $sortby = 'id';
	private $order = 1;
	private $tagfilter = [];
	private $tagcompare = 'OR';
	private $secure = 4;
	private $liento = ['min' => '0', 'max' => '0'];
	private $lienfrom = ['min' => '0', 'max' => '0'];
	private $col = ['id'];
	private $taglist = [];
	private $invert = 0;

	private $artvarlist;

	public function __construct(array $donnees = [])
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


	public function reset()
	{
		$varlist = get_class_vars(__class__);

		foreach ($varlist as $var => $default) {
			$method = 'set' . $var;
			$this->$method($default);
		}
	}

	// _______________________________________________ G E T _______________________________________________

	public function sortby()
	{
		return $this->sortby;
	}

	public function order()
	{
		return $this->order;
	}

	public function secure()
	{
		return $this->secure;
	}

	public function tagfilter($type = 'array')
	{
		return $this->tagfilter;
	}

	public function tagcompare()
	{
		return $this->tagcompare;
	}

	public function liento($type = 'array')
	{
		return $this->liento;
	}

	public function lienfrom($type = 'array')
	{
		return $this->lienfrom;
	}

	public function col($type = 'array')
	{
		if ($type == 'string') {
			return implode(', ', $this->col);
		} else {
			return ($this->col);
		}
	}

	public function taglist()
	{
		return $this->taglist;
	}

	public function invert()
	{
		return $this->invert;
	}

	public function artvarlist()
	{
		return $this->artvarlist;
	}


	// __________________________________________________ S E T _____________________________________________

	public function setsortby($sortby)
	{
		if (is_string($sortby) && in_array($sortby, $this->artvarlist())) {
			$this->sortby = strtolower(strip_tags($sortby));
		}
	}

	public function setorder($order)
	{
		$order = intval($order);
		if (in_array($order, [-1, 0, 1])) {
			$this->order = $order;
		}
	}

	public function settagfilter($tagfilter)
	{
		if (is_array($tagfilter)) {
			$this->tagfilter = $tagfilter;
		}
	}

	public function settagcompare($tagcompare)
	{
		if (in_array($tagcompare, ['OR', 'AND'])) {
			$this->tagcompare = $tagcompare;
		}
	}

	public function setsecure($secure)
	{
		if ($secure >= 0 && $secure <= 5) {
			$this->secure = intval($secure);
		}
	}

	public function setliento($range)
	{
		$this->liento = $range;
	}

	public function setlienfrom($range)
	{
		$this->lienfrom = $range;
	}

	public function setlientomin($min)
	{
		$this->liento['min'] = intval($min);
	}

	public function setlientomax($max)
	{
		$this->liento['max'] = intval($max);
	}

	public function setlienfrommin($min)
	{
		$this->lienfrom['min'] = intval($min);
	}

	public function setlienfrommax($max)
	{
		$this->lienfrom['max'] = intval($max);
	}

	public function setcol($col)
	{
		if (is_array($col)) {
			$this->col = array_intersect($this->artvarlist(), $col);
		}
	}


	public function settaglist(array $artlist)
	{
		$taglist = [];
		foreach ($artlist as $art) {
			foreach ($art->tag('array') as $tag) {
				if (!array_key_exists($tag, $taglist)) {
					$taglist[$tag] = 1;
				} else {
					$taglist[$tag]++;
				}
			}
		}
		$taglistsorted = arsort($taglist);
		$this->taglist = $taglist;
	}

	public function setinvert(int $invert)
	{
		if ($invert == 0 || $invert == 1) {
			$this->invert = $invert;
		}
	}


	public function setartvarlist(array $artvarlist)
	{
		$this->artvarlist = $artvarlist;
	}


}




?>