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

	public function get($art)
	{
		if($art instanceof Art2) {
			$id = $art->id();
		}
		$artdata = $this->artstore->findById($id);
		if($artdata !== false) {
			return new Art2($artdata);
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


	public function exist3($id)
	{

		$req = $this->bdd->prepare(' SELECT COUNT(*) FROM ' . Config::arttable() . ' WHERE id = :id ');
		$req->execute(array('id' => $id));
		$donnees = $req->fetch(PDO::FETCH_ASSOC);

		return (bool)$donnees['COUNT(*)'];
	}

	public function add3(Art2 $art)
	{

		if ($this->exist($art->id())) {
			echo '<span class="alert">idalreadyexist</span>';
		} else {

			$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));

			$table = Config::arttable();
			$request = "INSERT INTO  $table (id, title, description, tag, date, datecreation, datemodif, daterender, css, quickcss, javascript, html, header, section, nav, aside, footer, render, secure, invitepassword, interface, linkfrom, linkto, template, affcount, editcount)
						VALUES(:id, :title, :description, :tag, :date, :datecreation, :datemodif, :daterender, :css, :quickcss, :javascript, :html, :header, :section, :nav, :aside, :footer, :render, :secure, :invitepassword, :interface, :linkfrom, :linkto, :template, :affcount, :editcount)";

			$q = $this->bdd->prepare($request);

			$q->bindValue(':id', $art->id());
			$q->bindValue(':title', $art->title());
			$q->bindValue(':description', $art->description());
			$q->bindValue(':tag', $art->tag('string'));
			$q->bindValue(':date', $now->format('Y-m-d H:i:s'));
			$q->bindValue(':datecreation', $now->format('Y-m-d H:i:s'));
			$q->bindValue(':datemodif', $now->format('Y-m-d H:i:s'));
			$q->bindValue(':daterender', $now->format('Y-m-d H:i:s'));
			$q->bindValue(':css', $art->css());
			$q->bindValue(':quickcss', $art->quickcss('json'));
			$q->bindValue(':javascript', $art->javascript());
			$q->bindValue(':html', $art->html());
			$q->bindValue(':header', $art->header());
			$q->bindValue(':section', $art->md());
			$q->bindValue(':nav', $art->nav());
			$q->bindValue(':aside', $art->aside());
			$q->bindValue(':footer', $art->footer());
			$q->bindValue(':render', $art->render());
			$q->bindValue(':secure', $art->secure());
			$q->bindValue(':invitepassword', $art->invitepassword());
			$q->bindValue(':interface', $art->interface());
			$q->bindValue(':linkfrom', $art->linkfrom('json'));
			$q->bindValue(':linkto', $art->linkto('json'));
			$q->bindValue(':template', $art->template('json'));
			$q->bindValue(':affcount', $art->affcount());
			$q->bindValue(':editcount', $art->editcount());

			$q->execute();
		}
	}


	public function delete3(Art2 $art)
	{
		$req = $this->bdd->prepare('DELETE FROM ' . Config::arttable() . ' WHERE id = :id ');
		$req->execute(array('id' => $art->id()));
		$req->closeCursor();
	}

	public function get3(Art2 $art)
	{

		$req = $this->bdd->prepare('SELECT * FROM ' . Config::arttable() . ' WHERE id = :id ');
		$req->execute(array('id' => $art->id()));
		$donnees = $req->fetch(PDO::FETCH_ASSOC);

		return new Art2($donnees);

		$req->closeCursor();

	}




	public function update3(Art2 $art)
	{
		$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));

		//$request = 'UPDATE ' . $this->arttable . '(id, title, description, tag, date, datecreation, datemodif, daterender, css, quickcss, javascript, html, header, section, nav, aside, footer, render, secure, invitepassword, interface, linkfrom, template, affcount, editcount)	VALUES(:id, :title, :description, :tag, :date, :datecreation, :datemodif, :daterender, :css, :quickcss, :javascript, :html, :header, :section, :nav, :aside, :footer, :render, :secure, :invitepassword, :interface, :linkfrom, :template, :affcount, :editcount) WHERE id = :id';
		$table = Config::arttable();
		$request = "UPDATE $table SET id = :id, title = :title, description = :description, tag = :tag, date = :date, datecreation = :datecreation, datemodif = :datemodif, daterender = :daterender, css = :css, quickcss = :quickcss, javascript = :javascript, html = :html, header = :header, section = :section, nav = :nav, aside = :aside, footer = :footer, render = :footer, secure = :secure, invitepassword = :invitepassword, interface = :interface, linkfrom = :linkfrom, linkto = :linkto, template = :template, affcount = :affcount, editcount = :editcount WHERE id = :id";

		$q = $this->bdd->prepare($request);

		$q->bindValue(':id', $art->id());
		$q->bindValue(':title', $art->title());
		$q->bindValue(':description', $art->description());
		$q->bindValue(':tag', $art->tag('string'));
		$q->bindValue(':date', $art->date('string'));
		$q->bindValue(':datecreation', $art->datecreation('string'));
		$q->bindValue(':datemodif', $now->format('Y-m-d H:i:s'));
		$q->bindValue(':daterender', $art->daterender('string'));
		$q->bindValue(':css', $art->css());
		$q->bindValue(':quickcss', $art->quickcss('json'));
		$q->bindValue(':javascript', $art->javascript());
		$q->bindValue(':html', $art->html());
		$q->bindValue(':header', $art->header());
		$q->bindValue(':section', $art->md());
		$q->bindValue(':nav', $art->nav());
		$q->bindValue(':aside', $art->aside());
		$q->bindValue(':footer', $art->footer());
		$q->bindValue(':render', $art->render());
		$q->bindValue(':secure', $art->secure());
		$q->bindValue(':invitepassword', $art->invitepassword());
		$q->bindValue(':interface', $art->interface());
		$q->bindValue(':linkfrom', $art->linkfrom('json'));
		$q->bindValue(':linkto', $art->linkto('json'));
		$q->bindValue(':template', $art->template('json'));
		$q->bindValue(':affcount', $art->affcount());
		$q->bindValue(':editcount', $art->editcount());

		$q->execute();
	}




	public function getlister3(array $selection = ['id'], array $opt = [])
	{
		// give an array using SELECTION columns and sort and desc OPTIONS 

		$default = ['tri' => 'id', 'desc' => 'DESC'];
		$opt = array_update($default, $opt);

		$list = [];
		$option = ['datecreation', 'title', 'id', 'description', 'datemodif', 'tag', 'secure'];
		if (is_array($selection) && is_string($opt['tri']) && strlen($opt['tri']) < 16 && is_string($opt['desc']) && strlen($opt['desc']) < 5 && in_array($opt['tri'], $option)) {

			$selection = implode(", ", $selection);


			$select = 'SELECT ' . $selection . ' FROM ' . Config::arttable() . ' ORDER BY ' . $opt['tri'] . ' ' . $opt['desc'];
			$req = $this->bdd->query($select);
			while ($donnees = $req->fetch(PDO::FETCH_ASSOC)) {
				$list[] = new Art2($donnees);
			}
			return $list;
		}
	}



	public function getlisterwhere3(array $select = ['id'], array $whereid = [], $by = 'id', $order = 'DESC')
	{
		// give an array using SELECTION columns and sort and desc OPTIONS 


		$select = array_intersect(array_unique($select), self::SELECT);
		if (empty($select)) {
			$select = ' * ';
		} else {
			$select = implode(", ", $select);
		}


		$whereid = array_unique($whereid);

		$list = [];

		if (!in_array($by, self::BY)) {
			$by = 'id';
		}
		if (!in_array($order, self::ORDER)) {
			$order = 'DESC';
		}


		if (!empty($whereid)) {
			$wherestatements = array_map(function ($element) {
				return ' id= ?';
			}, $whereid);
			$where = 'WHERE ' . implode(' OR ', $wherestatements);
		} else {
			$where = '';
		}

		$table = Config::arttable();
		$prepare = "SELECT $select FROM $table $where ORDER BY $by $order";
		$req = $this->bdd->prepare($prepare);
		$req->execute($whereid);
		while ($donnees = $req->fetch(PDO::FETCH_ASSOC)) {
			$list[] = new Art2($donnees);
		}

		return $list;
	}






	public function getlisteropt(Opt $opt)
	{

		$artlist = [];

		$select = 'SELECT ' . $opt->col('string') . ' FROM ' . Config::arttable();
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
		$req = $this->bdd->query(' SELECT * FROM ' . Config::arttable() . ' ORDER BY id ');
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
