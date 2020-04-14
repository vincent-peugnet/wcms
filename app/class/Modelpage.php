<?php

namespace Wcms;

use Exception;
use JamesMoss\Flywheel\Document;
use DateTimeImmutable;


class Modelpage extends Modeldb
{

	protected $pagelist = [];


	public function __construct()
	{
		$this->dbinit(Model::PAGES_DIR);
		$this->storeinit(Config::pagetable());
		if(!$this->dircheck(Model::HTML_RENDER_DIR)) {
			throw new Exception("Media error : Cant create /render folder");
		}
	}

	/**
	 * Scan library for all pages as objects.
	 * If a scan has already been perform, it will just
	 * read `pagelist` Propriety
	 * 
	 * @return array of Pages objects as `id => Page`
	 */
	public function pagelist()
	{
		if(empty($this->pagelist)) {
			$list = $this->repo->findAll();
			foreach ($list as $pagedata) {
				$this->pagelist[$pagedata->id] = new Page($pagedata);
			}
		}
		return $this->pagelist;
	}


	/**
	 * Scan database for specific pages IDs and return array of Pages objects
	 * 
	 * @param array $idlist list of ID strings
	 * 
	 * @return Page[] array of Page objects
	 */
	public function pagelistbyid(array $idlist = []) : array
	{
		$pagedatalist = $this->repo->query()
		->where('__id', 'IN', $idlist)
		->execute();

		$pagelist = [];
		foreach ($pagedatalist as $id => $pagedata) {
			$pagelist[$id] = new Page($pagedata);
		}
		return $pagelist;
	}

	/**
	 * Store new page in the database
	 * 
	 * @param Page $page object
	 * @return bool depending on database storing
	 */
	public function add(Page $page) : bool
	{

		$pagedata = new Document($page->dry());
		$pagedata->setId($page->id());
		return $this->repo->store($pagedata);
	}

	/**
	 * Obtain a page object from the database
	 * 
	 * @param Page|string $id could be an Page object or a id string
	 * 
	 * @return Page|false The Page object or false if it does not exist.
	 */
	public function get($id)
	{
		if ($id instanceof Page) {
			$id = $id->id();
		}
		if (is_string($id)) {
			$pagedata = $this->repo->findById($id);
			if ($pagedata !== false) {
				return new Page($pagedata);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Transform File to Page Oject
	 * 
	 * @return false|Page 
	 */
	public function getfromfile()
	{
		if(!isset($_FILES['pagefile']) || $_FILES['pagefile']['error'] > 0 ) return false;

		$ext = substr(strrchr($_FILES['pagefile']['name'],'.'),1);
		if($ext !== 'json') return false;

		$files = $_FILES;

		$json = file_get_contents($_FILES['pagefile']['tmp_name']);
		$pagedata = json_decode($json, true);

		if($pagedata === false) return false;

		$page = new Page($pagedata);

		return $page;

	}

	public function getpageelement($id, $element)
	{
		if (in_array($element, Model::TEXT_ELEMENTS)) {
			$page = $this->get($id);
			if ($page !== false) {
				return $page->$element();
			} else {
				return false;
			}
		}
	}

	/**
	 * Delete a page and it's linked rendered html and css files
	 * 
	 * @param Page|string $id could be an Page object or a id string
	 * 
	 * @return bool true if success otherwise false
	 */
	public function delete($page) : bool
	{
		if ($page instanceof Page) {
			$page = $page->id();
		}
		if (is_string($page)) {
			$this->unlink($page);
			return $this->repo->delete($page);
		} else {
			return false;
		}
	}

	/**
	 * Delete rendered CSS and HTML files
	 */
	public function unlink(string $pageid)
	{
		$files = ['.css', '.quick.css', '.js'];
		foreach ($files as $file) {
			if (file_exists(Model::RENDER_DIR . $pageid . $file)) {
				unlink(Model::RENDER_DIR . $pageid . $file);
			}
		}
		if(file_exists(Model::HTML_RENDER_DIR . $pageid . '.html')) {
			unlink(Model::HTML_RENDER_DIR . $pageid . '.html');
		}
	}

	/**
	 * Update a page in the database
	 * 
	 * @todo Check if page already exist before updating ?
	 * 
	 * @param Page $page The page that is going to be updated
	 * 
	 * @return bool True if success otherwise, false
	 * 
	 */
	public function update(Page $page)
	{
		$pagedata = new Document($page->dry());
		$pagedata->setId($page->id());
		return $this->repo->store($pagedata);
	}

	public function combine(Page $pagea, Page $pageb)
	{
		$mergepage = $pagea;
		$merge = [];
		$diff = [];
			foreach ($pagea::TABS as $element) {
				if($pagea->$element() !== $pageb->$element()) {
					$merge[$element] = compare($pagea->$element(), $pageb->$element());
					$diff[] = $element;
				}
			}
		$mergepage->hydrate($merge);

		return ['diff' => $diff, 'mergepage' => $mergepage];
	}

	// public function diffpageelement(Page $pagea, Page $pageb)
	// {
	// 	$diff = [];
	// 	foreach ($pagea::TABS as $element) {
	// 		if($pagea->$element() !== $pageb->$element()) {
	// 			$diff[] = $element;
	// 		}
	// 	}
	// 	return $diff;
	// }

	public function pagecompare($page1, $page2, $method = 'id', $order = 1)
	{
		$result = ($page1->$method('sort') <=> $page2->$method('sort'));
		return $result * $order;
	}

	public function buildsorter($sortby, $order)
	{
		return function ($page1, $page2) use ($sortby, $order) {
			$result = $this->pagecompare($page1, $page2, $sortby, $order);
			return $result;
		};
	}



	public function pagelistsort(&$pagelist, $sortby, $order = 1)
	{
		return uasort($pagelist, $this->buildsorter($sortby, $order));
	}


	/**
	 * @param array $pagelist List of Page
	 * @param array $tagchecked list of tags
	 * @param string $tagcompare string, can be 'OR' or 'AND', set the tag filter method
	 * 
	 * @return array $array of `string` page id
	 */

	public function filtertagfilter(array $pagelist, array $tagchecked, $tagcompare = 'OR')
	{

		$filteredlist = [];
		foreach ($pagelist as $page) {
			if (empty($tagchecked)) {
				$filteredlist[] = $page->id();
			} else {
				$inter = (array_intersect($page->tag('array'), $tagchecked));
				if ($tagcompare == 'OR') {
					if (!empty($inter)) {
						$filteredlist[] = $page->id();
					}
				} elseif ($tagcompare == 'AND') {
					if (!array_diff($tagchecked, $page->tag('array'))) {
						$filteredlist[] = $page->id();
					}
				}
			}
		}
		return $filteredlist;
	}



	/**
	 * @param array $pagelist List of Page
	 * @param array $authorchecked list of authors
	 * @param string $authorcompare, can be 'OR' or 'AND', set the author filter method
	 * 
	 * @return array $array of `string` page id
	 */

	public function filterauthorfilter(array $pagelist, array $authorchecked, $authorcompare = 'OR')
	{

		$filteredlist = [];
		foreach ($pagelist as $page) {
			if (empty($authorchecked)) {
				$filteredlist[] = $page->id();
			} else {
				$inter = (array_intersect($page->authors('array'), $authorchecked));
				if ($authorcompare == 'OR') {
					if (!empty($inter)) {
						$filteredlist[] = $page->id();
					}
				} elseif ($authorcompare == 'AND') {
					if (!array_diff($authorchecked, $page->authors('array'))) {
						$filteredlist[] = $page->id();
					}
				}
			}
		}
		return $filteredlist;
	}

	/**
	 * @param array $pagelist List of Page
	 * @param int $secure secure level
	 * @param string $authorcompare, can be 'OR' or 'AND', set the author filter method
	 * 
	 * @return array $array of `string` page id
	 */

	public function filtersecure(array $pagelist, int $secure) : array
	{
		$filteredlist = [];
		foreach ($pagelist as $page) {
			if ($page->secure() == intval($secure)) {
				$filteredlist[] = $page->id();
			} elseif (intval($secure) >= 4) {
				$filteredlist[] = $page->id();
			}
		}
		return $filteredlist;
	}

	/**
	 * @param array $pagelist Array of Page object
	 * @param string $linkto
	 */
	public function filterlinkto(array $pagelist, string $linkto) : array
	{
		$filteredlist = [];
		foreach ($pagelist as $page) {
			if (in_array( $linkto, $page->linkto('array'))) {
				$filteredlist[] = $page->id();
			} elseif (empty($linkto)) {
				$filteredlist[] = $page->id();
			}
		}
		return $filteredlist;
	}


	public function tag(array $pagelist, $tagchecked)
	{
		$pagecheckedlist = [];
		foreach ($pagelist as $page) {
			if (in_array($tagchecked, $page->tag('array'))) {
				$pagecheckedlist[] = $page;
			}
		}
		return $pagecheckedlist;
	}

	public function taglist(array $pagelist, array $tagcheckedlist)
	{
		$taglist = [];
		foreach ($tagcheckedlist as $tag) {
			$taglist[$tag] = $this->tag($pagelist, $tag);
		}
		return $taglist;
	}

	/**
	 * @param array $taglist list of tags
	 * @param array $pagelist list of Page
	 * 
	 * @return array list of tags each containing list of id
	 */

	public function tagpagelist(array $taglist, array $pagelist)
	{
		$tagpagelist = [];
		foreach ($taglist as $tag) {
			$tagpagelist[$tag] = $this->filtertagfilter($pagelist, [$tag]);
		}
		return $tagpagelist;
	}

	public function lasteditedpagelist(int $last, array $pagelist)
	{
		$this->pagelistsort($pagelist, 'datemodif', -1);
		$pagelist = array_slice($pagelist, 0, $last);
		$idlist = [];
		foreach ($pagelist as $page) {
			$idlist[] = $page->id();
		}
		return $idlist;
	}

	/**
	 * Edit a page based on meta infos
	 * 
	 * @param string $pageid
	 * @param array $datas
	 * @param array $reset
	 * @param string $addtag
	 * @param string $addauthor
	 * 
	 * @return bool Depending on update success
	 */
	public function pageedit($pageid, $datas, $reset, $addtag, $addauthor) : bool
	{
		$page = $this->get($pageid);
		$page = $this->reset($page, $reset);
		$page->hydrate($datas);
		$page->addtag($addtag);
		$page->addauthor($addauthor);
		return $this->update($page);
	}

	/**
	 * Reset values of a page
	 * 
	 * @param Page $page Page object to be reseted
	 * @param array $reset List of parameters needing reset
	 * 
	 * @return Page The reseted page object
	 */
    public function reset(Page $page, array $reset) : Page
    {
		$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));
        if($reset['tag']) {
            $page->settag([]);
        }
        if($reset['author']) {
            $page->setauthors([]);
        }
        if($reset['redirection']) {
            $page->setredirection('');
        }
        if($reset['date']) {
			$page->setdate($now);
		}
        if($reset['datemodif']) {
			$page->setdatemodif($now);
		}
		return $page;
    }

}
