<?php
class Opt
{
	private $sortby = 'id';
	private $order = '1';
	private $tagor = [];
	private $secure = 4;
	private $liento = ['min' => '0', 'max' => '0'];
	private $lienfrom = ['min' => '0', 'max' => '0'];
	private $col = ['id'];
	private $taglist = [];

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

	public function dump()
	{
		var_dump($this);
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

	public function tagor($type = 'array')
	{
		return $this->tagor;
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

	public function artvarlist()
	{
		return $this->artvarlist;
	}

	public function taglist()
	{
		return $this->taglist;
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

	public function settagor($tagor)
	{
		if (is_array($tagor)) {
			// $tagorlist = [];
			// foreach ($tagor as $tag) {
			// 	if (array_key_exists($tag, $this->taglist())) {
			// 		$tagorlist[] = $tag;
			// 	}
			// }
			$this->tagor = $tagor;
		}
	}

	public function setsecure($secure)
	{
		if ($secure >= 0 && $secure <= 5) {
			$this->secure = intval($secure);
		}
	}

	public function setliento($n0, $n1)
	{
		$stock = [intval($n1), intval($n2)];
		$sorted = asort($stock);
		$this->liento = ['min' => $stock[0], 'max' => $stock[1]];
	}

	public function setlienfrom($n0, $n1)
	{
		$stock = [intval($n1), intval($n2)];
		$sorted = asort($stock);
		$this->lienfrom = ['min' => $stock[0], 'max' => $stock[1]];
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

	public function setartvarlist(array $artvarlist)
	{
		$this->artvarlist = $artvarlist;
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

}




?>