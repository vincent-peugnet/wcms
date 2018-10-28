<?php

class Modelrender extends Modelart
{
	const SUMMARY = '%SUMMARY%';

	
	public function __construct() {
		parent::__construct();
	}



	public function getelements(Art2 $art)
	{
		$templates = [];
		foreach ($art->template('array') as $element => $tempalteid) {
			if(isset($tempalteid) && $tempalteid != $art->id()) {				
				$templateart = new Art2(['id' => $templateid]);
				$templateart = $this->get($templateart);
				$templates[$element] = $templateart->$element();
			}
		}

		$elements = [];
		foreach ($art->template('array') as $element) {
			if(array_key_exists($element, $templates)) {
				$elements[$element] = $templates[$element] . PHP_EOL . $art->$element();
			} else {
				$elements[$element] = $art->$element();
			}
		}
		return $elements;		
	}

	public function elementsrender(array $elements)
	{
		foreach ($elements as $element => $text) {
			if(in_array($element, self::TEXT_ELEMENTS)) {
				$elements[$element] = $this->textrender($text);
			}
		}
		return $elements;
	}


	
	public function textrender($text)
	{
		
	}


	public function parser($text)
	{
		$text = str_replace('%TITLE%', $this->title(), $this->text);
		$text = str_replace('%DESCRIPTION%', $this->description(), $text);
		
		$text = $this->markdown($text);
		
		$text = str_replace('%SUMMARY%', sumparser($text), $text);
		
		$text = str_replace('href="=', 'href="?id=', $text);
		
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




	public function tooltip($linkfrom, $text)
	{
		$descriptions = [];
		$artlist = $app->getlisterwhere(['id', 'description'], $linkfrom);
		foreach ($artlist as $art) {
			$descriptions[$art->id()] = $art->description();
		}



		foreach ($linkfrom as $id) {
			if(isset($descriptions[$id])) {
				$title = $descriptions[$id];
			} else {
				$title = "This page does not exist yet";
			}
			$linkfrom = 'href="?id=' . $id . '"';
			$titlelinkfrom = ' title="' . $title . '" ' . $linkfrom;
			$text = str_replace($linkfrom, $titlelinkfrom, $text);
		}
	}

    public function parserff($text)
    {
        $section = str_replace('%TITLE%', $this->title(), $this->section);
		$section = str_replace('%DESCRIPTION%', $this->description(), $section);

	

		// replace = > ?id=
		$section = str_replace('href="=', 'href="?id=', $section);


		// infobulles tooltip




		if (!empty(strstr($section, '%SUMMARY%'))) {



			
		}


		$section = str_replace('href="./media/', ' class="file" target="_blank" href="./media/', $section);
		$section = str_replace('href="http', ' class="external" target="_blank" href="http', $section);
		$section = str_replace('<img src="/', '<img src="./media/', $section);
		$section = str_replace('<iframe', '<div class="iframe"><div class="container"><iframe class="video" ', $section);
		$section = str_replace('</iframe>', '</iframe></div></div>', $section);
		return $section;

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
				if($h > $last) {
					for ($i = 1; $i <= ($h - $last); $i++) {
						$sumstring .= '<ul>';
					}            
					$sumstring .= '<li><a href="#'.$title.'">'.$link.'</a></li>' ;
				} elseif ($h < $last) {
					for ($i = 1; $i <= ($last - $h); $i++) {
						$sumstring .= '</ul>';
					}
					$sumstring .= '<li><a href="#'.$title.'">'.$link.'</a></li>' ;            
				} elseif ($h = $last) {
					$sumstring .= '<li><a href="#'.$title.'">'.$link.'</a></li>' ;
				}
				$last = $h;
			}
		}
		for ($i = 1; $i <= ($last); $i++) {
			$sumstring .= '</ul>';
		}
		return $sumstring;
	}





}



?>