<?php

class Modelrender extends Modelart
{
	protected $router;
	protected $art;
	protected $artlist;
	protected $linkfrom = [];
	protected $sum = [];
	protected $internallinkblank = '';
	protected $externallinkblank = '';

	const SUMMARY = '%SUMMARY%';

	const RENDER_VERBOSE = 1;

	public function __construct($router)
	{
		parent::__construct();

		$this->router = $router;
		$this->artlist = $this->getlister();

		if(Config::internallinkblank()) {
			$this->internallinkblank = ' target="blank" ';
		}

		if(Config::externallinkblank()) {
			$this->externallinkblank = ' target="blank" ';
		}
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
		$body = $this->getbody($this->readbody());
		$parsebody = $this->parser($body);
		return $parsebody;
	}



	public function readbody()
	{
		if (!empty($this->art->templatebody())) {
			$templateid = $this->art->templatebody();
			$templateart = $this->get($templateid);
			$body = $templateart->body();
		} else {
			$body = $this->art->body();
		}
		$body = $this->article($body);
		$body = $this->automedialist($body);
		$body = $this->autotaglistupdate($body);
		return $body;
	}


	/**
	 * Analyse BODY, call the corresponding CONTENTs and render everything
	 * 
	 * @param string $body as the string BODY of the page
	 * 
	 * @return string as the full rendered BODY of the page
	 */
	public function getbody(string $body) : string
	{
		// Elements that can be detected
		$types = ['HEADER', 'NAV', 'MAIN', 'ASIDE', 'FOOTER'];

		// First level regex
		$regex = '~\%(' . implode("|", $types) . ')(\S*)\%~';

		// Match the first level regex
		preg_match_all($regex, $body, $out);

		// Create a list of all the elements that passed through the first level regex
		foreach ($out[0] as $key => $match) {
			$matches[$key] = ['fullmatch' => $match, 'type' => $out[1][$key], 'options' => $out[2][$key]];
		}

		// First, analyse the synthax and call the corresponding methods
		foreach ($matches as $key => $match) {
			$element = new Element($match, $this->art->id());
			$element->setcontent($this->getelementcontent($element));
			$element->setcontent($this->elementparser($element));
			$element->addtags();
			$body = str_replace($element->fullmatch(), $element->content(), $body);

		}



		return $body;

	}

	public function getelementcontent(Element $element)
	{
		$content = '';
		$subseparator = PHP_EOL . PHP_EOL;
		foreach($element->sources() as $source)
		{
			if($source !== $this->art->id()) {
				$subcontent = $this->getartelement($source, $element->type());
				if($subcontent !== false) {
					if(empty($subcontent && self::RENDER_VERBOSE > 0)) {
						$subcontent = PHP_EOL . '<!-- The ' . strtoupper($element->type()) . ' from page "' . $source . '" is currently empty ! -->' . PHP_EOL;
					}
				} else {
					$read = '<h2>Rendering error :</h2><p>The page <strong><code>' . $source . '</code></strong>, called in <strong><code>'. $element->fullmatch() . '</code></strong>, does not exist yet.</p>';
					throw new Exception($read);
				}

			} else {
				$type = $element->type();
				$subcontent = $this->art->$type();
			}
		$content .= $subseparator . $subcontent;
		}
		return $content . $subseparator;
	}

	public function elementparser(Element $element)
	{
		$content = $this->article($element->content());
		$content = $this->automedialist($content);
		$content = $this->autotaglistupdate($content);
		$content = $this->date($content);
		$content = $this->thumbnail($content);
		if($element->autolink()) {
			$content = str_replace('%LINK%', '' ,$content);
			$content = $this->everylink($content, $element->autolink());
		} else {
			$content = $this->taglink($content);
		}
		if($element->markdown()) {
			$content = $this->markdown($content);
		}

		return $content;
	}


	public function write()
	{
		file_put_contents(Model::RENDER_DIR . $this->art->id() . '.css', $this->art->css());
		//file_put_contents(Model::RENDER_DIR . $this->art->id() . '.quick.css', $this->art->quickcss());
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
		if (!empty($this->art->favicon())) {
			$head .= '<link rel="shortcut icon" href="' . Model::faviconpath() . $this->art->favicon() . '" type="image/x-icon">';
		} elseif (!empty(Config::defaultfavicon())) {
			$head .= '<link rel="shortcut icon" href="' . Model::faviconpath() . Config::defaultfavicon() . '" type="image/x-icon">';
		}
		$head .= '<meta name="description" content="' . $this->art->description() . '" />' . PHP_EOL;
		$head .= '<meta name="viewport" content="width=device-width" />' . PHP_EOL;


		$head .= '<meta property="og:title" content="' . $this->art->title() . '">' . PHP_EOL;
		$head .= '<meta property="og:description" content="' . $this->art->description() . '">' . PHP_EOL;
		$head .= '<meta property="og:image" content="' . Config::domain() . self::thumbnailpath() . $this->art->id() . '.jpg">' . PHP_EOL;
		$head .= '<meta property="og:url" content="' . Config::domain() . '">' . PHP_EOL;
		

		foreach ($this->art->externalcss() as $externalcss) {
			$head .= '<link href="' . $externalcss . '" rel="stylesheet" />' . PHP_EOL;
		}

		if (!empty($this->art->templatecss() && in_array('externalcss', $this->art->templateoptions()))) {
			$templatecss = $this->get($this->art->templatecss());
			foreach ($templatecss->externalcss() as $externalcss) {
				$head .= '<link href="' . $externalcss . '" rel="stylesheet" />' . PHP_EOL;
			}
		}

		foreach ($this->art->externalscript() as $externalscript) {
			$head .= '<script src="' . $externalscript . '"></script>' . PHP_EOL;
		}

		$head .= '<link href="' . Model::globalpath() . 'fonts.css" rel="stylesheet" />' . PHP_EOL;
		$head .= '<link href="' . Model::globalpath() . 'global.css" rel="stylesheet" />' . PHP_EOL;

		if (!empty($this->art->templatecss())) {
			$tempaltecssart = $this->art->templatecss();
			$head .= '<link href="' . Model::renderpath() . $tempaltecssart . '.css" rel="stylesheet" />' . PHP_EOL;
		}
		$head .= '<link href="' . Model::renderpath() . $this->art->id() . '.css" rel="stylesheet" />' . PHP_EOL;

		if (!empty($this->art->templatejavascript())) {
			$templatejsart = $this->art->templatejavascript();
			$head .= '<script src="' . Model::renderpath() . $templatejsart . '.js" async/></script>' . PHP_EOL;
		}
		$head .= '<script src="' . Model::renderpath() . $this->art->id() . '.js" async/></script>' . PHP_EOL;

		if (!empty(Config::analytics())) {

			$head .= PHP_EOL . '
			<!-- Global site tag (gtag.js) - Google Analytics -->
			<script async src="https://www.googletagmanager.com/gtag/js?id=' . Config::analytics() . '"></script>
			<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag(\'js\', new Date());

			gtag(\'config\', \'' . Config::analytics() . '\');
			</script>
			' . PHP_EOL;
		}


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
		$text = $this->media($text);

		$text = $this->headerid($text);

		$text = str_replace(self::SUMMARY, $this->sumparser($text), $text);

		$text = $this->wurl($text);
		$text = $this->wikiurl($text);

		$text = $this->desctitle($text, $this->art->description(), $this->art->title());


		$text = str_replace('href="http', ' class="external" target="_blank" href="http', $text);

		$text = $this->autourl($text);

		return $text;
	}

	public function media(string $text) : string
	{
		$text = preg_replace('%(src|href)="([\w-_]+(\/([\w-_])+)*\.[a-z0-9]{1,5})"%', '$1="' . Model::mediapath() . '$2" target="_blank" class="media"', $text);
		if (!is_string($text)) {
			throw new Exception('Rendering error -> media module');
		}
		return $text;
	}


	public function autourl($text)
	{
		$text = preg_replace('#( |\R|>)(https?:\/\/((\S+)\.([^< ]+)))#', '$1<a href="$2" class="external" '. $this->externallinkblank .'>$3</a>', $text);
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
					$link = 'href="' . $rend->uart($matches[1]) . '"" title="' . Config::existnot() . '" class="internal existnot"' . $this->internallinkblank;
				} else {
					$linkfrom[] = $matchart->id();
					$link =  'href="' . $rend->uart($matches[1]) . $matches[2] . '" title="' . $matchart->description() . '" class="internal exist '. $matchart->secure('string') .'"' . $this->internallinkblank;
				}
				return $link;
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
					return '<a href="' . $rend->uart($matches[1]) . '"" title="' . Config::existnot() . '" class="internal existnot" '. $this->internallinkblank .' >' . $matches[1] . '</a>';
				} else {
					$linkfrom[] = $matchart->id();
					return '<a href="' . $rend->uart($matches[1]) . $matches[2] . '" title="' . $matchart->description() . '" class="internal exist '. $matchart->secure('string') .'" '. $this->internallinkblank .' >' . $matchart->title() . '</a>';
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
		$text = preg_replace_callback(
			'/<h([1-6])(\s+(\s*\w+="\w+")*)?\s*>(.+)<\/h[1-6]>/mU',
			function ($matches) use (&$sum) {
				$cleanid = idclean($matches[4]);
				$sum[$cleanid][$matches[1]] = $matches[4];
				return '<h' . $matches[1] . $matches[2] . ' id="' . $cleanid . '">' . $matches[4] . '</h' . $matches[1] . '>';
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
		$text = preg_replace_callback($pattern, function ($matches) {
			if (!empty($matches[2])) {
				$id = ' id="' . $matches[2] . '" ';
			} else {
				$id = ' ';
			}
			return '<article ' . $id . '  markdown="1" >' . PHP_EOL . PHP_EOL . $matches[3] . PHP_EOL . PHP_EOL . '</article>' . PHP_EOL . PHP_EOL;
		}, $text);
		$text = preg_replace('/\R\R[=]{3,}([\w-]*)\R/', '', $text);
		return $text;
	}

	public function automedialist(string $text) : string
	{
		$text = preg_replace_callback(
			'~\%MEDIA:(([a-z0-9-_]+(\/([a-z0-9-_])+)*))\%~',
			function ($matches) {
				$dir = trim($matches[1], '/');
				$mediamanager = new Modelmedia();



				if (is_dir(Model::MEDIA_DIR . $dir)) {
					$medialist = $mediamanager->getlistermedia(Model::MEDIA_DIR . $dir . '/');

					$dirid = str_replace('/', '-', $dir);

					$ul = '<ul class="medialist" id="' . $dirid . '">' . PHP_EOL;

					foreach ($medialist as $media) {
						$ul .= '<li>';
						if ($media->type() == 'image') {
							$ul .= '<img alt="' . $media->id() . '" id="' . $media->id() . '" src="' . $media->getincludepath() . '" >';
						} elseif ($media->type() == 'sound') {
							$ul .= '<audio id="' . $media->id() . '" controls src="' . $media->getincludepath() . '" </audio>';
						} elseif ($media->type() == 'video') {
							$ul .= '<video controls><source src="' . $media->getincludepath() . '" type="video/' . $media->extension() . '"></video>';
						} elseif ($media->type() == 'other') {
							$ul .= '<a href="' . $media->getincludepath() . '" target="_blank" class="media" >' . $media->id() . '.' . $media->extension() . '</a>';
						}
						$ul .= '</li>' . PHP_EOL;
					}

					$ul .= '</ul>' . PHP_EOL;

					return $ul;
				} else {
					return 'directory "' . $dir . '" not found';
				}
			},
			$text
		);

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
		$pattern = "/\%TAG:([a-z0-9_-]+)\%/";
		preg_match_all($pattern, $text, $out);
		return $out[1];

	}

	public function autotaglistupdate($text)
	{
		$taglist = $this->autotaglist($text);
		foreach ($taglist as $tag) {
			$li = [];
			foreach ($this->artlist as $item) {
				if (in_array($tag, $item->tag('array'))) {
					$li[] = $item;
				}

			}
			$ul = '<ul class="taglist" id="' . $tag . '">' . PHP_EOL;
			$this->artlistsort($li, 'date', -1);
			foreach ($li as $item) {
				if ($item->id() === $this->art->id()) {
					$actual = ' actualpage';
				} else {
					$actual = '';
				}
				$ul .= '<li><a href="' . $this->router->generate('artread/', ['art' => $item->id()]) . '" title="' . $item->description() . '" class="internal' . $actual . '" '. $this->internallinkblank .'  >' . $item->title() . '</a></li>' . PHP_EOL;
			}
			$ul .= '</ul>' . PHP_EOL;


			$text = str_replace('%TAG:' . $tag . '%', $ul, $text);

			$li = array_map(function ($item) {
				return $item->id();
			}, $li);
			$this->linkfrom = array_unique(array_merge($this->linkfrom, $li));
		}
		return $text;
	}


	public function date(string $text)
	{
		$art = $this->art;
		$text = preg_replace_callback('~\%DATE\%~', function ($matches) use ($art) {
			return '<time datetime=' . $art->date('string') . '>' . $art->date('dmy') . '</time>';
		}, $text);
		$text = preg_replace_callback('~\%TIME\%~', function ($matches) use ($art) {
			return '<time datetime=' . $art->date('string') . '>' . $art->date('ptime') . '</time>';
		}, $text);

		return $text;
	}

	/**
	 * Render thumbnail of the page
	 * 
	 * @param string $text Text to analyse
	 * 
	 * @return string The rendered output
	 */
	public function thumbnail(string $text) : string
	{
		$img = '<img class="thumbnail" src="' . Model::thumbnailpath() . $this->art->id() . '.jpg" alt="' . $this->art->title() . '">';
		$img = PHP_EOL . $img . PHP_EOL;
		$text = str_replace('%THUMBNAIL%', $img, $text);

		return $text;
	}

	public function taglink($text)
	{
		$rend = $this;
		$text = preg_replace_callback('/\%LINK\%(.*)\%LINK\%/msU', function ($matches) use ($rend) {
			return $rend->everylink($matches[1], 1);
		}, $text);
		return $text;
	}

	/**
	 * Autolink Function : transform every word of more than $limit characters in internal link
	 * 
	 * @param string $text The input text to be converted
	 * 
	 * @return string Conversion output
	 */
	public function everylink(string $text, int $limit) : string
	{
		$regex = '~([\w-_éêèùïüîçà]{' . $limit . ',})~';
		$text = preg_replace_callback($regex , function ($matches) {
			return '<a href="' . idclean($matches[1]) . '">' . $matches[1] . '</a>';
		}, $text);
		return $text;
	}



	public function linkfrom()
	{
		sort($this->linkfrom);
		$linkfrom = $this->linkfrom;
		$this->linkfrom = [];
		return $linkfrom;
	}

	public function linkto()
	{
		$linkto = [];
		foreach ($this->artlist as $art) {
			if (in_array($this->art->id(), $art->linkfrom())) {
				$linkto[] = $art->id();
			}
		}
		return $linkto;
	}




}



?>