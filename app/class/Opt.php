<?php

namespace Wcms;

class Opt extends Item
{
	protected $sortby = 'id';
	protected $order = 1;
	protected $tagfilter = [];
	protected $tagcompare = 'AND';
	protected $authorfilter = [];
	protected $authorcompare = 'AND';
	protected $secure = 4;
	protected $linkto = ['min' => '0', 'max' => '0'];
	protected $linkfrom = ['min' => '0', 'max' => '0'];
	protected $col = ['id'];
	protected $taglist = [];
	protected $authorlist = [];
	protected $invert = 0;
	protected $limit = 0;

	protected $pagevarlist;

	public function __construct(array $donnees = [])
	{
		$this->hydrate($donnees);
	}





	public function resetall()
	{
		$varlist = get_class_vars(__class__);

		foreach ($varlist as $var => $default) {
			$method = 'set' . $var;
			$this->$method($default);
		}
	}

	public function reset($var)
	{
		$varlist = get_class_vars(__class__);
		if (in_array($var, $varlist)) {
			$this->$var = $varlist[$var];
		}
	}

	public function submit()
	{
		if (isset($_GET['submit'])) {
			if ($_GET['submit'] == 'reset') {
				$_SESSION['opt'] = [];
			} elseif ($_GET['submit'] == 'filter') {
				$this->getall();
			}
		} else {
			$this->sessionall();
		}
	}

	public function getall()
	{
		$optlist = ['sortby', 'order', 'secure', 'tagcompare', 'tagfilter', 'authorcompare', 'authorfilter', 'limit', 'invert'];

		foreach ($optlist as $method) {
			if (method_exists($this, $method)) {
				if (isset($_GET[$method])) {
					$setmethod = 'set' . $method;
					$this->$setmethod($_GET[$method]);
				} else {
					$this->reset($method);
				}
				$_SESSION['opt'][$method] = $this->$method();
			}
		}
	}

	public function sessionall()
	{
		if (isset($_SESSION['opt'])) {
			$this->hydrate($_SESSION['opt']);
		}
	}

	public function getadress()
	{
		$object = $this->drylist(['sortby', 'order', 'secure', 'tagfilter', 'tagcompare', 'authorfilter', 'authorcompare', 'invert', 'limit']);
		$object['submit'] = 'filter';

		return '?' . urldecode(http_build_query($object));
	}

	public function sortbyorder($sortby = "")
	{
		$object = $this->drylist(['sortby', 'order', 'secure', 'tagfilter', 'tagcompare', 'authorfilter', 'authorcompare', 'invert', 'limit']);
		if (!empty($sortby)) {
			$object['sortby'] = $sortby;
		}
		$object['order'] = $object['order'] * -1;
		$object['submit'] = 'filter';

		return '?' . urldecode(http_build_query($object));
	}

	/**
	 * Get the link list for each tags of an page
	 * 
	 * @param array $taglist List of tag to be
	 * @return string html code to be printed
	 */
	public function taglinks(array $taglist = []): string
	{
		$tagstring = "";
		foreach ($taglist as $tag) {
			$tagstring .= '<a class="tag tag_' . $tag . '" href="?' . $this->getfilteradress(['tagfilter' => [$tag]]) . '" >' . $tag . '</a>' . PHP_EOL;
		}
		return $tagstring;
	}

	/**
	 * Get the link list for each authors of an page
	 * 
	 * @param array $authorlist List of author to be
	 * @return string html code to be printed
	 */
	public function authorlinks(array $authorlist = []): string
	{
		$authorstring = "";
		foreach ($authorlist as $author) {
			$authorstring .= '<a class="author author_' . $author . '" href="?' . $this->getfilteradress(['authorfilter' => [$author]]) . '" >' . $author . '</a>' . PHP_EOL;
		}
		return $authorstring;
	}

	public function securelink(int $level, string $secure)
	{
		return '<a class="secure ' . $secure . '" href="?' . $this->getfilteradress(['secure' => $level]) . '">' . $secure . '</a>' . PHP_EOL;
	}


	public function getfilteradress(array $vars = [])
	{
		$varlist = ['sortby', 'order', 'secure', 'tagfilter', 'tagcompare', 'authorfilter', 'authorcompare', 'invert', 'limit'];
		// array_filter($vars, function ())
		$object = $this->drylist($varlist);
		$object = array_merge($object, $vars);
		$object['submit'] = 'filter';
		return urldecode(http_build_query($object));
	}



	/**
	 * Get the query as http string
	 * 
	 * @return string The resulted query
	 */
	public function getquery(): string
	{
		$class = get_class_vars(get_class($this));
		$object = get_object_vars($this);
		$class['pagevarlist'] = $object['pagevarlist'];
		$class['taglist'] = $object['taglist'];
		$class['authorlist'] = $object['authorlist'];
		$query = array_diff_assoc_recursive($object, $class);

		return urldecode(http_build_query($query));
	}

	public function parsetagcss(string $cssstring)
	{
		$classprefix = 'tag';
		$pattern = '%a\.' . $classprefix . '\_([a-z0-9\-\_]*)\s*\{\s*(background-color):\s*(#[A-F0-6]{6})\;\s*\}%';
		preg_match($pattern, $cssstring, $matches);
		foreach ($matches as $value) {
		}
	}

	public function tocss($cssdatas)
	{
		$string = '';
		foreach ($cssdatas as $element => $css) {
			$string .= PHP_EOL . $element . ' {';
			foreach ($css as $param => $value) {
				$string .= PHP_EOL . '    ' . $param . ': ' . $value . ';';
			}
			$string .= PHP_EOL . '}' . PHP_EOL;
		}
		return $string;
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

	public function authorfilter($type = 'array')
	{
		return $this->authorfilter;
	}

	public function authorcompare()
	{
		return $this->authorcompare;
	}

	public function linkto($type = 'array')
	{
		return $this->linkto;
	}

	public function linkfrom($type = 'array')
	{
		return $this->linkfrom;
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

	public function authorlist()
	{
		return $this->authorlist;
	}

	public function invert()
	{
		return $this->invert;
	}

	public function pagevarlist()
	{
		return $this->pagevarlist;
	}

	public function limit()
	{
		return $this->limit;
	}


	// __________________________________________________ S E T _____________________________________________

	public function setsortby($sortby)
	{
		if (is_string($sortby) && in_array($sortby, $this->pagevarlist) && in_array($sortby, Model::COLUMNS)) {
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
		if (!empty($tagfilter) && is_array($tagfilter)) {
			$tagfilterverif = [];
			foreach ($tagfilter as $tag) {
				if (array_key_exists($tag, $this->taglist)) {
					$tagfilterverif[] = $tag;
				}
			}
			$this->tagfilter = $tagfilterverif;
		}
	}

	public function settagcompare($tagcompare)
	{
		if (in_array($tagcompare, ['OR', 'AND'])) {
			$this->tagcompare = $tagcompare;
		}
	}

	public function setauthorfilter($authorfilter)
	{
		if (!empty($authorfilter) && is_array($authorfilter)) {
			$authorfilterverif = [];
			foreach ($authorfilter as $author) {
				if (array_key_exists($author, $this->authorlist)) {
					$authorfilterverif[] = $author;
				}
			}
			$this->authorfilter = $authorfilterverif;
		}
	}

	public function setauthorcompare($authorcompare)
	{
		if (in_array($authorcompare, ['OR', 'AND'])) {
			$this->authorcompare = $authorcompare;
		}
	}

	public function setsecure($secure)
	{
		if ($secure >= 0 && $secure <= 5) {
			$this->secure = intval($secure);
		}
	}

	public function setlinkto($range)
	{
		$this->linkto = $range;
	}

	public function setlinkfrom($range)
	{
		$this->linkfrom = $range;
	}

	public function setlinktomin($min)
	{
		$this->linkto['min'] = intval($min);
	}

	public function setlinktomax($max)
	{
		$this->linkto['max'] = intval($max);
	}

	public function setlinkfrommin($min)
	{
		$this->linkfrom['min'] = intval($min);
	}

	public function setlinkfrommax($max)
	{
		$this->linkfrom['max'] = intval($max);
	}

	public function setcol($col)
	{
		if (is_array($col)) {
			$this->col = array_intersect($this->pagevarlist, $col);
		}
	}

	public function settaglist(array $pagelist)
	{
		$taglist = [];
		foreach ($pagelist as $page) {
			foreach ($page->tag('array') as $tag) {
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

	public function setauthorlist(array $pagelist)
	{
		$authorlist = [];
		foreach ($pagelist as $page) {
			foreach ($page->authors('array') as $author) {
				if (!array_key_exists($author, $authorlist)) {
					$authorlist[$author] = 1;
				} else {
					$authorlist[$author]++;
				}
			}
		}
		$authorlistsorted = arsort($authorlist);
		$this->authorlist = $authorlist;
	}

	public function setinvert(int $invert)
	{
		if ($invert == 0 || $invert == 1) {
			$this->invert = $invert;
		} else {
			$this->invert = 0;
		}
	}

	public function setlimit($limit)
	{
		$limit = intval($limit);
		if ($limit < 0) {
			$limit = 0;
		} elseif ($limit >= 10000) {
			$limit = 9999;
		}
		$this->limit = $limit;
	}


	public function setpagevarlist(array $pagevarlist)
	{
		$this->pagevarlist = $pagevarlist;
	}
}
