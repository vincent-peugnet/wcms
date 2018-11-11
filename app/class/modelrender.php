<?php

class Modelrender extends Modelart
{
	const SUMMARY = '%SUMMARY%';


	public function __construct()
	{
		parent::__construct();
	}

	public function renderhead(Art2 $art)
	{

		$head = $this->gethead($art);
		$this->write($art);
		return $head;
	}

	public function renderbody(Art2 $art)
	{
		$body = $this->getbody($this->gethtml($art), $this->getelements($art));
		$parsebody = $this->parser($art, $body);
		return $parsebody;
	}



	public function getelements(Art2 $art)
	{
		$elements = [];
		foreach (self::TEXT_ELEMENTS as $element) {
			if (isset($art->template('array')[$element])) {
				$templateid = $art->template('array')[$element];
				$tempalteart = $this->get($templateid);
				$text = $tempalteart->$element() . PHP_EOL . $art->$element();
			} else {
				$text = $art->$element();
			}
			$elements[$element] = PHP_EOL . '<' . $element . '>' . PHP_EOL . $this->markdown($text) . PHP_EOL . '</' . $element . '>' . PHP_EOL;

		}

		return $elements;
	}

	public function gethtml(Art2 $art)
	{
		if (isset($art->template('array')['html'])) {
			$templateid = $art->template('array')['html'];
			$tempalteart = $this->get($templateid);
			$html = $tempalteart->html() . PHP_EOL . $art->html();
		} else {
			$html = $art->html();
		}
		return $html;
	}

	public function getbody(string $html, array $elements)
	{
		$html = preg_replace_callback('~\%(SECTION|ASIDE|NAV|HEADER|FOOTER)\%~', function ($match) use ($elements) {
			return $elements[strtolower($match[1])];
		}, $html);
		return $html;
	}

	public function write(Art2 $art)
	{
		file_put_contents(Model::RENDER_DIR . $art->id() . '.css', $art->css());
		file_put_contents(Model::RENDER_DIR . $art->id() . '.quick.css', $art->quickcss());
		file_put_contents(Model::RENDER_DIR . $art->id() . '.js', $art->javascript());
	}

	public function writetemplates(Art2 $art)
	{
		if (array_key_exists('css', $art->template('array'))) {
			$tempaltecssart = $this->get($art->template('array')['css']);
			file_put_contents(Model::RENDER_DIR . $tempaltecssart->id() . '.css', $tempaltecssart->css());
		}
		if (array_key_exists('quickcss', $art->template('array'))) {
			$tempaltequickcssart = $this->get($art->template('array')['quickcss']);
			file_put_contents(Model::RENDER_DIR . $tempaltequickcssart->id() . '.quick.css', $tempaltequickcssart->quickcss());
		}
		if (array_key_exists('javascript', $art->template('array'))) {
			$templatejsart = $this->get($art->template('array')['javascript']);
			file_put_contents(Model::RENDER_DIR . $templatejsart->id() . '.js', $templatejsart->javascript());
		}
	}




	public function gethead(Art2 $art)
	{

		$head = '';

		$head .= '<meta charset="utf8" />' . PHP_EOL;
		$head .= '<title>' . $art->title() . '</title>' . PHP_EOL;
		$head .= '<meta name="description" content="' . $art->description() . '" />' . PHP_EOL;
		$head .= '<meta name="viewport" content="width=device-width" />' . PHP_EOL;

		if (isset($art->template('array')['quickcss'])) {
			$tempaltequickcssart = $art->template('array')['quickcss'];
			$head .= '<link href="' . Model::renderpath() . $tempaltequickcssart . '.quick.css" rel="stylesheet" />' . PHP_EOL;
		}
		$head .= '<link href="' . Model::renderpath() . $art->id() . '.quick.css" rel="stylesheet" />' . PHP_EOL;

		if (isset($art->template('array')['css'])) {
			$tempaltecssart = $art->template('array')['css'];
			$head .= '<link href="' . Model::renderpath() . $tempaltecssart . '.css" rel="stylesheet" />' . PHP_EOL;
		}
		$head .= '<link href="' . Model::renderpath() . $art->id() . '.css" rel="stylesheet" />' . PHP_EOL;

		if (isset($art->template('array')['javascript'])) {
			$templatejsart = $art->template('array')['javascript'];
			$head .= '<script src="' . Model::renderpath() . $templatejsart . '.js" async/></script>' . PHP_EOL;
		}
		$head .= '<script src="' . Model::renderpath() . $art->id() . '.js" async/></script>' . PHP_EOL;

		return $head;
	}

	public function elementsrender(Art2 $art)
	{
		foreach ($this->getelements($art) as $element => $text) {
			if (in_array($element, self::TEXT_ELEMENTS)) {
				$elements[$element] = $this->markdown($text);
			}
		}
		return $elements;
	}



	public function parser(Art2 $art, string $text)
	{
		$text = str_replace('%TITLE%', $art->title(), $text);
		$text = str_replace('%DESCRIPTION%', $art->description(), $text);


		$text = str_replace(self::SUMMARY, $this->sumparser($text), $text);

		$text = str_replace('href="=', 'href="?id=', $text);

		$text = $this->tooltip($art->linkfrom('array'), $text);

		$text = str_replace('href="http', ' class="external" target="_blank" href="http', $text);
		$text = str_replace('<img src="/', '<img src="./media/', $text);

		$text = $this->autourl($text);

		return $text;
	}


	public function autourl($text)
	{
		$text = preg_replace('#( |\R|>)(https?:\/\/((\S+)\.([^< ]+)))#', '$1<a href="$2" class="external" target="_blank">$3</a>', $text);
		return $text;
	}


	public function markdown($text)
	{		
		//use Michelf\MarkdownExtra;
		$fortin = new Michelf\MarkdownExtra;
		// id in headers
		$fortin->header_id_func = function ($header) {
			return preg_replace('/[^\w]/', '', strtolower($header));
		};
		$fortin->hard_wrap = true;
		$text = $fortin->transform($text);
		return $text;
	}




	public function tooltip(array $linkfrom, string $text)
	{
		$descriptions = [];
		$artlist = $this->getlisterid($linkfrom);
		foreach ($artlist as $art) {
			$descriptions[$art->id()] = $art->description();
		}

		foreach ($linkfrom as $id) {
			if (isset($descriptions[$id])) {
				$linkfrom = 'href="?id=' . $id . '"';
				$titlelinkfrom = ' title="' . $descriptions[$id] . '" ' . $linkfrom;
				$text = str_replace($linkfrom, $titlelinkfrom, $text);
			}
		}
		return $text;
	}



	function sumparser($text)
	{
		preg_match_all('#<h([1-6]) id="(\w+)">(.+)</h[1-6]>#iU', $text, $out);


		$sum = [];
		foreach ($out[2] as $key => $value) {
			$sum[$value][$out[1][$key]] = $out[3][$key];
		}


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



	//tag auto menu


	public function autotaglist()
	{
		$pattern = "/%%(\w*)%%/";
		preg_match_all($pattern, $this->md(), $out);
		return $out[1];

	}

	public function autotaglistupdate($taglist)
	{
		foreach ($taglist as $tag => $artlist) {
			$replace = '<ul>';
			foreach ($artlist as $art) {
				$replace .= '<li><a href="?id=' . $art->id() . '" title="' . $art->description() . '">' . $art->title() . '</a></li>';
			}
			$replace .= '</ul>';
			$text = str_replace('%%' . $tag . '%%', $replace, $text);
		}
	}

	public function autotaglistcalc($taglist)
	{
		foreach ($taglist as $tag => $artlist) {
			foreach ($artlist as $art) {
				if (!in_array($art->id(), $this->linkfrom('array')) && $art->id() != $this->id()) {
					$this->linkfrom[] = $art->id();
				}
			}
		}
	}



}



?>