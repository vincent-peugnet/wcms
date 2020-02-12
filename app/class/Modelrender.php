<?php

namespace Wcms;

use Exception;
use Michelf\MarkdownExtra;

class Modelrender extends Modelpage
{
	protected $router;
	/** @var Page */
	protected $page;
	/** @var array list of ID as strings */
	protected $pagelist;
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
		$this->pagelist = $this->list();

		if(Config::internallinkblank()) {
			$this->internallinkblank = ' target="_blank" ';
		}

		if(Config::externallinkblank()) {
			$this->externallinkblank = ' target="_blank" ';
		}
	}

	/**
	 * Used to convert the markdown user manual to html document
	 * 
	 * @param string $text Input text in markdown
	 * @return string html formated text
	 */
	public function rendermanual(string $text) : string
	{
		$text = $this->markdown($text);
		$text = $this->headerid($text, 5);
		return $text;

	}


	/**
	 * Generate page relative link for given page_id including basepath
	 * 
	 * @param string $id given page ID
	 * 
	 * @return string Relative URL
	 */
	public function upage(string $id) : string
	{
		return $this->router->generate('pageread/', ['page' => $id]);
	}


	/**
	 * Main function
	 * 
	 * @param Page $page page to render
	 */
	public function render(Page $page)
	{
		$this->page = $page;

		$this->write($this->gethmtl());
	}

	/**
	 * Combine body and head to create html file
	 * 
	 * @return string html string
	 */
	public function gethmtl()
	{

		$head = $this->gethead();
		$body = $this->getbody($this->readbody());
		$parsebody = $this->parser($body);

		$html = '<!DOCTYPE html>' . PHP_EOL . '<html>' . PHP_EOL . '<head>' . PHP_EOL . $head . PHP_EOL . '</head>' . PHP_EOL . $parsebody . PHP_EOL . '</html>';
	
		return $html;
	}


	public function readbody()
	{
		if (!empty($this->page->templatebody())) {
			$templateid = $this->page->templatebody();
			$templatepage = $this->get($templateid);
			if($templatepage !== false) {
				$body = $templatepage->body();
			} else {
				$body = $this->page->body();
				$this->page->settemplatebody('');
			}
		} else {
			$body = $this->page->body();
		}
		$body = $this->article($body);
		$body = $this->automedialist($body);
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
		$regex = '~\%(' . implode("|", $types) . ')(\?([\S]+))?\%~';

		// Match the first level regex
		preg_match_all($regex, $body, $out);

		// Create a list of all the elements that passed through the first level regex
		foreach ($out[0] as $key => $match) {
			$matches[$key] = ['fullmatch' => $match, 'type' => $out[1][$key], 'options' => $out[3][$key]];
		}


		// First, analyse the synthax and call the corresponding methods
		if(isset($matches)) {
			foreach ($matches as $key => $match) {
				$element = new Element($match, $this->page->id());
				$element->setcontent($this->getelementcontent($element->sources(), $element->type()));
				$element->setcontent($this->elementparser($element));
				$element->addtags();
				$body = str_replace($element->fullmatch(), $element->content(), $body);
	
			}
		}



		return $body;

	}

	/**
	 * Foreach $sources (pages), this will get the corresponding $type element content
	 * 
	 * @param array $sources Array of pages ID
	 * @param string $type Type of element 
	 */
	public function getelementcontent(array $sources, string $type)
	{
		$content = '';
		$subseparator = PHP_EOL . PHP_EOL;
		foreach($sources as $source)
		{
			if($source !== $this->page->id()) {
				$subcontent = $this->getpageelement($source, $type);
				if($subcontent !== false) {
					if(empty($subcontent && self::RENDER_VERBOSE > 0)) {
						$subcontent = PHP_EOL . '<!-- The ' . strtoupper($type) . ' from page "' . $source . '" is currently empty ! -->' . PHP_EOL;
					}
				} else {
					$read = '<h2>Rendering error :</h2><p>The page <strong><code>' . $source . '</code></strong>, does not exist yet.</p>';
					//throw new Exception($read);
				}

			} else {
				$subcontent = $this->page->$type();
			}
		$content .= $subseparator . $subcontent;
		}
		return $content . $subseparator;
	}

	public function elementparser(Element $element)
	{
		$content = $this->article($element->content());
		$content = $this->automedialist($content);
		$content = $this->pagelist($content);
		$content = $this->date($content);
		$content = $this->thumbnail($content);
		if($element->autolink()) {
			$content = $this->everylink($content, $element->autolink());
		}
		if($element->markdown()) {
			$content = $this->markdown($content);
		}

		return $content;
	}


	/**
	 * Write css javascript and html as files in the assets folder
	 */
	public function write(string $html)
	{
		file_put_contents(Model::HTML_RENDER_DIR . $this->page->id() . '.html', $html);
		file_put_contents(Model::RENDER_DIR . $this->page->id() . '.css', $this->page->css());
		//file_put_contents(Model::RENDER_DIR . $this->page->id() . '.quick.css', $this->page->quickcss());
		file_put_contents(Model::RENDER_DIR . $this->page->id() . '.js', $this->page->javascript());
	}



	public function writetemplates()
	{
		if (array_key_exists('css', $this->page->template('array'))) {
			$tempaltecsspage = $this->get($this->page->template('array')['css']);
			file_put_contents(Model::RENDER_DIR . $tempaltecsspage->id() . '.css', $tempaltecsspage->css());
		}
		if (array_key_exists('javascript', $this->page->template('array'))) {
			$templatejspage = $this->get($this->page->template('array')['javascript']);
			file_put_contents(Model::RENDER_DIR . $templatejspage->id() . '.js', $templatejspage->javascript());
		}
	}




	public function gethead()
	{

		$head = '';

		$head .= '<meta charset="utf-8" />' . PHP_EOL;
		$head .= '<title>' . $this->page->title() . '</title>' . PHP_EOL;
		if (!empty($this->page->favicon())) {
			$head .= '<link rel="shortcut icon" href="' . Model::faviconpath() . $this->page->favicon() . '" type="image/x-icon">';
		} elseif (!empty(Config::defaultfavicon())) {
			$head .= '<link rel="shortcut icon" href="' . Model::faviconpath() . Config::defaultfavicon() . '" type="image/x-icon">';
		}
		$head .= '<meta name="description" content="' . $this->page->description() . '" />' . PHP_EOL;
		$head .= '<meta name="viewport" content="width=device-width" />' . PHP_EOL;


		$head .= '<meta property="og:title" content="' . $this->page->title() . '">' . PHP_EOL;
		$head .= '<meta property="og:description" content="' . $this->page->description() . '">' . PHP_EOL;
		
		if(!empty($this->page->thumbnail())) {
			$head .= '<meta property="og:image" content="' . Config::domain() . self::thumbnailpath() . $this->page->thumbnail() . '">' . PHP_EOL;
		} elseif(!empty(Config::defaultthumbnail())) {
			$head .= '<meta property="og:image" content="' . Config::domain() . self::thumbnailpath() . Config::defaultthumbnail() . '">' . PHP_EOL;
		}
		
		$head .= '<meta property="og:url" content="' . Config::url() . $this->page->id() . '/">' . PHP_EOL;
		

		foreach ($this->page->externalcss() as $externalcss) {
			$head .= '<link href="' . $externalcss . '" rel="stylesheet" />' . PHP_EOL;
		}

		if (!empty($this->page->templatecss() && in_array('externalcss', $this->page->templateoptions()))) {
			$templatecss = $this->get($this->page->templatecss());
			if($templatecss !== false) {

				foreach ($templatecss->externalcss() as $externalcss) {
					$head .= '<link href="' . $externalcss . '" rel="stylesheet" />' . PHP_EOL;
				}
			}
		}

		$head .= PHP_EOL . $this->page->customhead() . PHP_EOL;


		$head .= '<link href="' . Model::globalpath() . 'fonts.css" rel="stylesheet" />' . PHP_EOL;
		$head .= '<link href="' . Model::globalpath() . 'global.css" rel="stylesheet" />' . PHP_EOL;

		if (!empty($this->page->templatecss())) {
			$tempaltecsspage = $this->page->templatecss();
			$head .= '<link href="' . Model::renderpath() . $tempaltecsspage . '.css" rel="stylesheet" />' . PHP_EOL;
		}
		$head .= '<link href="' . Model::renderpath() . $this->page->id() . '.css" rel="stylesheet" />' . PHP_EOL;

		if (!empty($this->page->templatejavascript())) {
			$templatejspage = $this->page->templatejavascript();
			$head .= '<script src="' . Model::renderpath() . $templatejspage . '.js" async/></script>' . PHP_EOL;
		}
		if (!empty($this->page->javascript())) {
			$head .= '<script src="' . Model::renderpath() . $this->page->id() . '.js" async/></script>' . PHP_EOL;
		}

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
		
		if (!empty($this->page->redirection())) {
			if (preg_match('%https?:\/\/\S*%', $this->page->redirection(), $out)) {
				$url = $out[0];
				$head .= PHP_EOL . '<meta http-equiv="refresh" content="' . $this->page->refresh() . '; URL=' . $url . '" />';
			} elseif (in_array($this->page->redirection(), $this->pagelist)) {
				$url = $this->upage($this->page->redirection());
				$head .= PHP_EOL . '<meta http-equiv="refresh" content="' . $this->page->refresh() . '; URL=' . $url . '" />';
			}
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

		$text = str_replace(self::SUMMARY, $this->sumparser(), $text);

		$text = $this->wurl($text);
		$text = $this->wikiurl($text);

		$text = $this->desctitle($text, $this->page->description(), $this->page->title());


		$text = str_replace('href="http', ' class="external" target="_blank" href="http', $text);

		$text = $this->autourl($text);

		$text = $this->authenticate($text);

		return $text;
	}

	public function media(string $text) : string
	{
		$text = preg_replace('%(src|href)="([\w-_]+(\/([\w-_])+)*\.[a-z0-9]{1,5})"%', '$1="' . Model::mediapath() . '$2" target="_blank" class="media"', $text);
		if (!is_string($text)) {
			//throw new Exception('Rendering error -> media module');
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
				$matchpage = $rend->get($matches[1]);
				if (!$matchpage) {
					$link = 'href="' . $rend->upage($matches[1]) . '"" title="' . Config::existnot() . '" class="internal existnot"' . $this->internallinkblank;
				} else {
					$linkfrom[] = $matchpage->id();
					$link =  'href="' . $rend->upage($matches[1]) . $matches[2] . '" title="' . $matchpage->description() . '" class="internal exist '. $matchpage->secure('string') .'"' . $this->internallinkblank;
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
				$matchpage = $rend->get($matches[1]);
				if (!$matchpage) {
					return '<a href="' . $rend->upage($matches[1]) . '"" title="' . Config::existnot() . '" class="internal existnot" '. $this->internallinkblank .' >' . $matches[1] . '</a>';
				} else {
					$linkfrom[] = $matchpage->id();
					return '<a href="' . $rend->upage($matches[1]) . $matches[2] . '" title="' . $matchpage->description() . '" class="internal exist '. $matchpage->secure('string') .'" '. $this->internallinkblank .' >' . $matchpage->title() . '</a>';
				}
			},
			$text
		);
		$this->linkfrom = array_unique(array_merge($this->linkfrom, $linkfrom));
		return $text;
	}

	/**
	 * Add Id to html header elements and store the titles in the `sum` parameter
	 * 
	 * @param string $text Input html document to scan
	 * @param int $maxdeepness Maximum header deepness to look for. Min = 1 Max = 6 Default = 6
	 * 
	 * @return string text with id in header
	 */

	public function headerid($text, int $maxdeepness = 6)
	{
		if($maxdeepness > 6 || $maxdeepness < 1) {
			$maxdeepness = 6;
		}


		$sum = [];
		$text = preg_replace_callback(
			'/<h([1-' . $maxdeepness . '])(\s+(\s*\w+="\w+")*)?\s*>(.+)<\/h[1-' . $maxdeepness . ']>/mU',
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
		$fortin = new MarkdownExtra;
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

	/**
	 * Check for media list call in the text and insert media list
	 * @param string $text Text to scan and replace
	 * 
	 * @return string Output text
	 */
	public function automedialist(string $text)
	{
		preg_match_all('~\%MEDIA\?([a-zA-Z0-9\[\]\&=\-\/\%]*)\%~', $text, $out);

		foreach ($out[0] as $key => $match) {
			$matches[$key] = ['fullmatch' => $match, 'filter' => $out[1][$key]];
		}

		if(isset($matches)) {
			foreach ($matches as $match) {
				$medialist = new Medialist($match);
				$medialist->readfilter();
				$text = str_replace($medialist->fullmatch(), $medialist->generatecontent(), $text);
			}
		}
		return $text;
	}

	/**
	 * Generate a Summary based on header ids. Need to use `$this->headerid` before to scan text
	 *  
	 * @param int $min Minimum header deepness to start the summary : Between 1 and 6.
	 * @param int $max Maximum header deepness to start the summary : Between 1 and 6.
	 * 
	 * @return string html list with anchor link
	 */
	function sumparser(int $min = 1, int $max = 6) : string
	{
		$min = $min >= 1 && $min <= 6 && $min <= $max ? $min : 1;
		$end = $max >=1 && $max <= 6 && $max >= $min ? $max : 6;

		$sum = $this->sum;

		$filteredsum = [];

		foreach ($sum as $key => $menu) {
			$deepness = array_keys($menu)[0];
			if($deepness >= $min && $deepness <= $max) {
				$filteredsum[$key] = $menu;
			}
		}

		$sumstring = '';
		$last = 0;
		foreach ($filteredsum as $title => $list) {
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


	public function date(string $text)
	{
		$page = $this->page;
		$text = preg_replace_callback('~\%DATE\%~', function ($matches) use ($page) {
			return '<time datetime=' . $page->date('string') . '>' . $page->date('dmy') . '</time>';
		}, $text);
		$text = preg_replace_callback('~\%TIME\%~', function ($matches) use ($page) {
			return '<time datetime=' . $page->date('string') . '>' . $page->date('ptime') . '</time>';
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
		$img = '<img class="thumbnail" src="' . Model::thumbnailpath() . $this->page->id() . '.jpg" alt="' . $this->page->title() . '">';
		$img = PHP_EOL . $img . PHP_EOL;
		$text = str_replace('%THUMBNAIL%', $img, $text);

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
		$regex = '~([\w-_éêèùïüîçà]{' . $limit . ',})(?![^<]*>|[^<>]*<\/)~';
		$text = preg_replace_callback($regex , function ($matches) {
			return '<a href="' . idclean($matches[1]) . '">' . $matches[1] . '</a>';
		}, $text);
		return $text;
	}



	/**
	 * @param string $text content to analyse and replace
	 * 
	 * @return string text ouput
	 */
	public function authenticate(string $text)
	{
		$id = $this->page->id();
		$regex = '~\%CONNECT(\?dir=([a-zA-Z0-9-_]+))?\%~';
		$text = preg_replace_callback($regex, function ($matches) use ($id) {
			if(isset($matches[2])) {
				$id = $matches[2];
			}
			$form = '<form action="' . $this->dirtopath('!co') . '" method="post">
			<input type="password" name="pass" id="loginpass" placeholder="password">
			<input type="hidden" name="route" value="pageread/">
			<input type="hidden" name="id" value="' . $id . '">
			<input type="submit" name="log" value="login" id="button">
			</form>';
			return $form;

		}, $text);
		return $text;
	}

	/**
	 * Render pages list
	 */
	public function pagelist(string $text) : string
	{
		preg_match_all('~\%LIST\?([a-zA-Z0-9\]\[\&=\-\/\%]*)\%~', $text, $out);

		foreach ($out[0] as $key => $match) {
			$matches[$key] = ['fullmatch' => $match, 'options' => $out[1][$key]];
		}

		$modelhome = new Modelhome();

		if(isset($matches)) {
			$pagelist = $this->getlister();

			foreach ($matches as $match) {
				$optlist = $modelhome->Optlistinit($pagelist);
				$optlist->parsehydrate($match['options']);
				$table2 = $modelhome->table2($pagelist, $optlist, '', []);

				$content = '<ul class="pagelist">' . PHP_EOL ;
				foreach ($table2 as $page ) {
					$content .= '<li>' . PHP_EOL;
					$content .= '<a href="' . $this->upage($page->id()) . '">' . $page->title() . '</a>' . PHP_EOL;
					if($optlist->description()) {
						$content .= '<em>' . $page->description() . '</em>' . PHP_EOL;
					}
					if($optlist->date()) {
						$content .= '<code>' . $page->date('pdate') . '</code>' . PHP_EOL;
					}
					if($optlist->time()) {
						$content .= '<code>' . $page->date('ptime') . '</code>' . PHP_EOL;
					}
					if($optlist->author()) {
						$content .=  $page->authors('string') . PHP_EOL;
					}
					$content .= '</li>';
				}
				$content .= '</ul>';

				$text = str_replace($match['fullmatch'], $content, $text);
			}
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