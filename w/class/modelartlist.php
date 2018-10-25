<?php

class Modelartlist extends Modeldb
{
    

	public function getlister(array $selection = ['id'], array $opt = [])
	{
		// give an array using SELECTION columns and sort and desc OPTIONS 

		$default = ['tri' => 'id', 'desc' => 'DESC'];
		$opt = array_update($default, $opt);

		$list = [];
		$option = ['datecreation', 'title', 'id', 'description', 'datemodif', 'tag', 'secure'];
		if (is_array($selection) && is_string($opt['tri']) && strlen($opt['tri']) < 16 && is_string($opt['desc']) && strlen($opt['desc']) < 5 && in_array($opt['tri'], $option)) {

			$selection = implode(", ", $selection);

			$select = 'SELECT ' . $selection . ' FROM ' . $this->arttable . ' ORDER BY ' . $opt['tri'] . ' ' . $opt['desc'];
			$req = $this->bdd->query($select);
			while ($donnees = $req->fetch(PDO::FETCH_ASSOC)) {
				$list[] = new Art2($donnees);
			}
			return $list;
		}
	}






	public function getlisteropt(Opt $opt)
	{

		$artlist = [];

		$select = 'SELECT ' . $opt->col('string') . ' FROM ' . $this->arttable;
		$req = $this->bdd->query($select);
		while ($donnees = $req->fetch(PDO::FETCH_ASSOC)) {
			$artlist[] = new Art2($donnees);
		}
		return $artlist;

	}

	public function listcalclinkfrom(&$artlist)
	{
		foreach ($artlist as $art) {
			$art->calclinkto($artlist);
		}
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


	public function lister()
	{
		$req = $this->bdd->query(' SELECT * FROM ' . $this->arttable . ' ORDER BY id ');
		$donnees = $req->fetchAll(PDO::FETCH_ASSOC);
		$req->closeCursor();
		return $donnees;

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





?>