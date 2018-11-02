<?php

class Modelrender extends Modelart
{
	const SUMMARY = '%SUMMARY%';


	public function __construct()
	{
		parent::__construct();
	}


	public function render(Art2 $art)
	{
		$body = $this->getbody($art);
		
		var_dump($body);
		
		$parsebody = $this->parser($art, $body);
		
		echo $parsebody;

		return $parsebody;
	}



	public function getbody(Art2 $art)
	{
		$body = '';
		foreach (self::TEXT_ELEMENTS as $element) {
			if (array_key_exists($element, $art->template('array'))) {
				$tempalteart = $this->get($art->template('array')[$element]);
				$text = $tempalteart->$element() . PHP_EOL . $art->$element();
			} else {
				$text = $art->$element();
			}
			$body .= PHP_EOL . '<' . $element . '>' . PHP_EOL . $this->markdown($text) . PHP_EOL . '</' . $element . '>' . PHP_EOL;
		}

		return $body;
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
				$title = $descriptions[$id];
			}
			$linkfrom = 'href="?id=' . $id . '"';
			$titlelinkfrom = ' title="' . $title . '" ' . $linkfrom;
			$text = str_replace($linkfrom, $titlelinkfrom, $text);
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