<?php

class Modelrender extends Modelart
{
	protected $router;
	protected $art;
	protected $artlist;
	protected $linkfrom = [];
	protected $sum = [];

	const SUMMARY = '%SUMMARY%';


	public function __construct($router)
	{
		parent::__construct();

		$this->router = $router;
		$this->artlist = $this->getlister();
	}

	public function uart($id)
	{
		return $this->router->generate('artread/', ['art' => $id]);
	}

	public function renderhead(Art2 $art)
	{
		$this->art = $art;

		$head = $this->gethead();
		$this->write();
		return $head;
	}

	public function renderbody(Art2 $art)
	{
		$this->art = $art;
		$body = $this->getbody($this->readbody(), $this->getelements());
		$parsebody = $this->parser($body);
		return $parsebody;
	}



	public function getelements()
	{
		$elements = [];
		foreach (self::TEXT_ELEMENTS as $element) {
			if (isset($this->art->template('array')[$element])) {
				$templateid = $this->art->template('array')[$element];
				$tempalteart = $this->get($templateid);
				if($tempalteart) {
					$text = $tempalteart->$element() . PHP_EOL . $this->art->$element();
				} else {
					$text = $this->art->$element();
				}
			} else {
				$text = $this->art->$element();
			}
			$text = $this->article($text);
			$text = $this->autotaglistupdate($text);
			$text = $this->markdown($text);
			
			$elements[$element] = PHP_EOL . '<' . $element . '>' . PHP_EOL . $text . PHP_EOL . '</' . $element . '>' . PHP_EOL;

		}

		return $elements;
	}

	public function readbody()
	{
		if (isset($this->art->template('array')['body'])) {
			$templateid = $this->art->template('array')['body'];
			$tempalteart = $this->get($templateid);
			$body = $tempalteart->body() . PHP_EOL . $this->art->body();
		} else {
			$body = $this->art->body();
		}
		return $body;
	}

	public function getbody(string $body, array $elements)
	{
		$body = preg_replace_callback('~\%(SECTION|ASIDE|NAV|HEADER|FOOTER)\%~', function ($match) use ($elements) {
			return $elements[strtolower($match[1])];
		}, $body);
		return $body;
	}

	public function write()
	{
		file_put_contents(Model::RENDER_DIR . $this->art->id() . '.css', $this->art->css());
		file_put_contents(Model::RENDER_DIR . $this->art->id() . '.quick.css', $this->art->quickcss());
		file_put_contents(Model::RENDER_DIR . $this->art->id() . '.js', $this->art->javascript());
	}

	public function writetemplates()
	{
		if (array_key_exists('css', $this->art->template('array'))) {
			$tempaltecssart = $this->get($this->art->template('array')['css']);
			file_put_contents(Model::RENDER_DIR . $tempaltecssart->id() . '.css', $tempaltecssart->css());
		}
		if (array_key_exists('quickcss', $this->art->template('array'))) {
			$tempaltequickcssart = $this->get($this->art->template('array')['quickcss']);
			file_put_contents(Model::RENDER_DIR . $tempaltequickcssart->id() . '.quick.css', $tempaltequickcssart->quickcss());
		}
		if (array_key_exists('javascript', $this->art->template('array'))) {
			$templatejsart = $this->get($this->art->template('array')['javascript']);
			file_put_contents(Model::RENDER_DIR . $templatejsart->id() . '.js', $templatejsart->javascript());
		}
	}




	public function gethead()
	{

		$head = '';

		$head .= '<meta charset="utf8" />' . PHP_EOL;
		$head .= '<title>' . $this->art->title() . '</title>' . PHP_EOL;
		$head .= '<meta name="description" content="' . $this->art->description() . '" />' . PHP_EOL;
		$head .= '<meta name="viewport" content="width=device-width" />' . PHP_EOL;
		$head .= '<link href="' . Model::globalpath() . 'fonts.css" rel="stylesheet" />' . PHP_EOL;
		$head .= '<link href="' . Model::globalpath() . 'global.css" rel="stylesheet" />' . PHP_EOL;

		if (isset($this->art->template('array')['quickcss'])) {
			$tempaltequickcssart = $this->art->template('array')['quickcss'];
			$head .= '<link href="' . Model::renderpath() . $tempaltequickcssart . '.quick.css" rel="stylesheet" />' . PHP_EOL;
		}
		$head .= '<link href="' . Model::renderpath() . $this->art->id() . '.quick.css" rel="stylesheet" />' . PHP_EOL;

		if (isset($this->art->template('array')['css'])) {
			$tempaltecssart = $this->art->template('array')['css'];
			$head .= '<link href="' . Model::renderpath() . $tempaltecssart . '.css" rel="stylesheet" />' . PHP_EOL;
		}
		$head .= '<link href="' . Model::renderpath() . $this->art->id() . '.css" rel="stylesheet" />' . PHP_EOL;

		if (isset($this->art->template('array')['javascript'])) {
			$templatejsart = $this->art->template('array')['javascript'];
			$head .= '<script src="' . Model::renderpath() . $templatejsart . '.js" async/></script>' . PHP_EOL;
		}
		$head .= '<script src="' . Model::renderpath() . $this->art->id() . '.js" async/></script>' . PHP_EOL;

		return $head;
	}

	public function desctitle($text, $desc, $title)
	{
		$text = str_replace('%TITLE%', $title, $text);
		$text = str_replace('%DESCRIPTION%', $desc, $text);
		return $text;
	}


	public function parser(string $text)
	{

		$text = $this->headerid($text);

		$text = str_replace(self::SUMMARY, $this->sumparser($text), $text);

		$text = $this->wurl($text);
		$text = $this->wikiurl($text);

		$text = $this->desctitle($text, $this->art->description(), $this->art->title());


		$text = str_replace('href="http', ' class="external" target="_blank" href="http', $text);
		$text = str_replace('<img src="/', '<img class="local" src="'. Model::mediapath(), $text);
		$text = str_replace('<a href="/', '<a class="media" target="_blank" href="'. Model::mediapath(), $text);

		$text = $this->autourl($text);

		return $text;
	}


	public function autourl($text)
	{
		$text = preg_replace('#( |\R|>)(https?:\/\/((\S+)\.([^< ]+)))#', '$1<a href="$2" class="external" target="_blank">$3</a>', $text);
		return $text;
	}

	public function wurl(string $text)
	{
		$linkfrom = [];
		$rend = $this;
		$text = preg_replace_callback(
			'%href="([\w-]+)\/?(#?[a-z-_]*)"%',
			function ($matches) use ($rend, &$linkfrom) {
				$matchart = $rend->get($matches[1]);
				if (!$matchart) {
					return 'href="' . $rend->uart($matches[1]) . '"" title="' . Config::existnot() . '" class="internal"';
				} else {
					$linkfrom[] = $matchart->id();
					return 'href="' . $rend->uart($matches[1]) . $matches[2]. '" title="' . $matchart->description() . '" class="internal"';
				}
			},
			$text
		);
		$this->linkfrom = array_unique(array_merge($this->linkfrom, $linkfrom));
		return $text;
	}

	public function wikiurl(string $text)
	{
		$linkfrom = [];
		$rend = $this;
		$text = preg_replace_callback(
			'%\[([\w-]+)\/?#?([a-z-_]*)\]%',
			function ($matches) use ($rend, &$linkfrom) {
				$matchart = $rend->get($matches[1]);
				if (!$matchart) {
					return '<a href="' . $rend->uart($matches[1]) . '"" title="' . Config::existnot() . '" class="internal">' . $matches[1] . '</a>';
				} else {
					$linkfrom[] = $matchart->id();		
					return '<a href="' . $rend->uart($matches[1]) . $matches[2].'" title="' . $matchart->description() . '" class="internal">' . $matchart->title() . '</a>';
				}
			},
			$text
		);
		$this->linkfrom = array_unique(array_merge($this->linkfrom, $linkfrom));
		return $text;
	}

	public function headerid($text)
	{
		$sum = [];
		$text = preg_replace_callback('/<h([1-6])(\s+(\s*\w+="\w+")*)?\s*>(.+)<\/h[1-6]>/mU',
		function ($matches) use (&$sum) {
			$cleanid = idclean($matches[4]);
			$sum[$cleanid][$matches[1]] = $matches[4];
			return '<h'.$matches[1] . $matches[2] . ' id="'.$cleanid.'">'.$matches[4].'</h'.$matches[1].'>';
		},
		$text
	);
	$this->sum = $sum;
	return $text;
	}

	public function markdown($text)
	{		
		//use Michelf\MarkdownExtra;
		$fortin = new Michelf\MarkdownExtra;
		// id in headers
		// $fortin->header_id_func = function ($header) {
		// 	return preg_replace('/[^\w]/', '', strtolower($header));
		// };
		$fortin->hard_wrap = true;
		$text = $fortin->transform($text);
		return $text;
	}



	public function article($text)
	{
		$pattern = '/(\R\R|^\R|^)[=]{3,}([\w-]*)\R\R(.*)(?=\R\R[=]{3,}[\w-]*\R)/sUm';
		$text = preg_replace_callback($pattern, function($matches) {
			if(!empty($matches[2])) {
				$id = ' id="'.$matches[2].'" ';
			} else {
				$id = ' ';
			}
			return '<article ' . $id . '  markdown="1" >' . PHP_EOL . PHP_EOL . $matches[3] . PHP_EOL . PHP_EOL . '</article>' . PHP_EOL . PHP_EOL;
		} , $text);
		$text = preg_replace('/\R\R[=]{3,}([\w-]*)\R/', '', $text);
		return $text;
	}



	function sumparser($text)
	{
		preg_match_all('#<h([1-6]) id="(\w+)">(.+)</h[1-6]>#iU', $text, $out);


		$sum = $this->sum;



		$sumstring = '';
		$last = 0;
		foreach ($sum as $title => $list) {
			foreach ($list as $h => $link) {
				if ($h > $last) {
					for ($i = 1; $i <= ($h - $last); $i++) {
						$sumstring .= '<ul>';
					}
					$sumstring .= '<li><a href="#' . $title . '">' . $link . '</a></li>';
				} elseif ($h < $last) {
					for ($i = 1; $i <= ($last - $h); $i++) {
						$sumstring .= '</ul>';
					}
					$sumstring .= '<li><a href="#' . $title . '">' . $link . '</a></li>';
				} elseif ($h = $last) {
					$sumstring .= '<li><a href="#' . $title . '">' . $link . '</a></li>';
				}
				$last = $h;
			}
		}
		for ($i = 1; $i <= ($last); $i++) {
			$sumstring .= '</ul>';
		}
		return $sumstring;
	}



	public function autotaglist($text)
	{
		$pattern = "/%%(\w*)%%/";
		preg_match_all($pattern, $text, $out);
		return $out[1];

	}

	public function autotaglistupdate($text)
	{
		$taglist = $this->autotaglist($text);
		foreach ($taglist as $tag ) {
			$li = [];
			foreach ($this->artlist as $item ) {
				if(in_array($tag, $item->tag('array'))) {
					$li[] = $item;
				}
				
			}
			$ul = '<ul id="'.$tag.'">' . PHP_EOL;
			$this->artlistsort($li, 'date', -1);
				foreach ($li as $item ) {
					if($item->id() === $this->art->id()) {
						$actual = ' actualpage';
					} else {
						$actual = '';
					}
					$ul .= '<li><a href="'.$this->router->generate('artread/', ['art' => $item->id()]).'" title="'.$item->description().'" class="internal'.$actual.'"  >'.$item->title().'</a></li>' . PHP_EOL;
				}
			$ul .= '</ul>' . PHP_EOL;

			
			$text = str_replace('%%'.$tag.'%%', $ul, $text);

			$li = array_map(function($item) {
				return $item->id();
			}, $li);
			$this->linkfrom = array_unique(array_merge($this->linkfrom, $li));
		}
		return $text;
	}

	public function linkfrom()
	{
		sort($this->linkfrom);
		$linkfrom = $this->linkfrom;
		$this->linkfrom = [];
		return $linkfrom;
	}




}



?>