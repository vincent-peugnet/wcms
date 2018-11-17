<?php
class Modelart extends Modeldb
{

	const SELECT = ['title', 'id', 'description', 'tag', 'date', 'datecreation', 'datemodif', 'daterender', 'css', 'quickcss', 'javascript', 'html', 'header', 'section', 'nav', 'aside', 'footer', 'render', 'secure', 'invitepassword', 'interface', 'linkfrom', 'linkto', 'template', 'affcount', 'editcount'];
	const BY = ['datecreation', 'title', 'id', 'description', 'datemodif', 'secure'];
	const ORDER = ['DESC', 'ASC'];


	public function __construct()
	{
		parent::__construct();
	}



	public function exist(Art2 $art)
	{
		$artdata = $this->artstore->get($art->id());
		if($artdata === false) {
			return false;
		} else {
			return true;
		}
		
	}


	public function add(Art2 $art)
	{

		$artdata = new \JamesMoss\Flywheel\Document($art->dry());
		$artdata->setId($art->id());
		$this->artstore->store($artdata);
	}

	public function get($id)
	{
		if($id instanceof Art2) {
			$id = $id->id();
		}
		if(is_string($id)) {
			$artdata = $this->artstore->findById($id);
			if($artdata !== false) {
				return new Art2($artdata);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function delete(Art2 $art)
	{
		$this->artstore->delete($art->id());
	}

	public function update(Art2 $art)
	{
		$artdata = new \JamesMoss\Flywheel\Document($art->dry());
		$artdata->setId($art->id());
		$this->artstore->store($artdata);
	}

	public function artcompare($art1, $art2, $method = 'id', $order = 1)
	{
		$result = ($art1->$method('sort') <=> $art2->$method('sort'));
		return $result * $order;
	}

	public function buildsorter($sortby, $order)
	{
		return function ($art1, $art2) use ($sortby, $order) {
			$result = $this->artcompare($art1, $art2, $sortby, $order);
			return $result;
		};
	}



	public function artlistsort(&$artlist, $sortby, $order = 1)
	{
		return usort($artlist, $this->buildsorter($sortby, $order));
	}






	public function filtertagfilter(array $artlist, array $tagchecked, $tagcompare = 'OR')
	{

		$filteredlist = [];
		foreach ($artlist as $art) {
			if (empty($tagchecked)) {
				$filteredlist[] = $art->id();
			} else {
				$inter = (array_intersect($art->tag('array'), $tagchecked));
				if ($tagcompare == 'OR') {
					if (!empty($inter)) {
						$filteredlist[] = $art->id();
					}
				} elseif ($tagcompare == 'AND') {
					if (!array_diff($tagchecked, $art->tag('array'))) {
						$filteredlist[] = $art->id();
					}
				}
			}
		}
		return $filteredlist;
	}

	public function filtersecure(array $artlist, $secure)
	{
		$filteredlist = [];
		foreach ($artlist as $art) {
			if ($art->secure() == intval($secure)) {
				$filteredlist[] = $art->id();
			} elseif (intval($secure) >= 4) {
				$filteredlist[] = $art->id();
			}
		}
		return $filteredlist;
	}


	public function tag(array $artlist, $tagchecked)
	{
		$artcheckedlist = [];
		foreach ($artlist as $art) {
			if (in_array($tagchecked, $art->tag('array'))) {
				$artcheckedlist[] = $art;
			}
		}
		return $artcheckedlist;
	}

	public function taglist(array $artlist, array $tagcheckedlist)
	{
		$taglist = [];
		foreach ($tagcheckedlist as $tag) {
			$taglist[$tag] = $this->tag($artlist, $tag);
		}
		return $taglist;
	}

	public function count()
	{
		return $this->bdd->query(' SELECT COUNT(*) FROM ' . $this->arttable . ' ')->fetchColumn();
	}

}
