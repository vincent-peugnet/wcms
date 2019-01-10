<?php


function class_autoloader($class)
{
    require('.'. DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . strtolower(str_replace('\\', DIRECTORY_SEPARATOR, $class)) . '.php');
}




function readablesize($bytes)
{

	$num = 5;
	$location = 'tree';
	$format = ' %d %s';



	if ($bytes < 2 ** 10) {
		$num = $bytes;
		$unit = 'o';
	} elseif ($bytes < 2 ** 20) {
		$num = round($bytes / 2 ** 10, 1);
		$unit = 'Kio';
	} elseif ($bytes < 2 ** 30) {
		$num = round($bytes / 2 ** 20, 1);
		$unit = 'Mio';
	} elseif ($bytes < 2 ** 40) {
		$num = round($bytes / 2 ** 30, 1);
		$unit = 'Gio';
	}

	return sprintf($format, $num, $unit);
}

/* human readable date interval
 * @param DateInterval $diff - l'interval de temps
 * @return string
 */
function hrdi(DateInterval $diff)
{
	$str = "";
	if ($diff->y > 1) return $str . $diff->y . ' years';
	if ($diff->y == 1) return $str . ' 1 year and ' . $diff->m . ' months';
	if ($diff->m > 1) return $str . $diff->m . ' months';
	if ($diff->m == 1) return $str . ' 1 month and ' . $diff->d . ($diff->d > 1 ? ' days' : ' day');
	if ($diff->d > 1) return $str . $diff->d . ' days';
	if ($diff->d == 1) return $str . ' 1 day and ' . $diff->h . ($diff->h > 1 ? ' hours' : ' hour');
	if ($diff->h > 1) return $str . $diff->h . ' hours';
	if ($diff->h == 1) return $str . ' 1 hour and ' . $diff->i . ($diff->i > 1 ? ' minutes' : ' minute');
	if ($diff->i > 1) return $str . $diff->i . ' minutes';
	if ($diff->i == 1) return $str . ' 1 minute';
	return $str . ' a few secondes';
}



function arrayclean($input)
{
	$output = [];
	foreach ($input as $key => $value) {
		if (is_array($value)) {
			$output[$key] = array_filter($value);
		} else {
			$output[$key] = $value;
		}
	}
	return $output;
}

function idclean(string $input)
{	
	$input = urldecode($input);
	$search =  ['é', 'à', 'è', 'ç', 'ù', 'ï', 'î', ' '];
	$replace = ['e', 'a', 'e', 'c', 'u', 'i', 'i', '-'];
	$input = str_replace($search, $replace, $input);

	$input = preg_replace('%[^a-z0-9-_+]%', '', strtolower(trim($input)));

	$input = substr($input, 0, Model::MAX_ID_LENGTH);

	return $input;
}


function getversion()
{
	if(file_exists('composer.json')) {
		$composer = json_decode(file_get_contents('composer.json'), true);
		$version = $composer['version'];
	} else {
		$version = 'unknown';
	}
	return $version;
}



function array_update($base, $new)
{
	foreach ($base as $key => $value) {
		if (array_key_exists($key, $new)) {
			if (gettype($base[$key]) == gettype($new[$key])) {
				$base[$key] = $new[$key];
			}
		}
	}
	return $base;
}

function contains($needle, $haystack)
{
    return strpos($haystack, $needle) !== false;
}


function str_clean(string $string)
{
	return str_replace(' ', '_', strtolower(strip_tags($string)));
}




function changekey($array, $oldkey, $newkey)
{
	if (!array_key_exists($oldkey, $array))
		return $array;

	$keys = array_keys($array);
	$keys[array_search($oldkey, $keys)] = $newkey;

	return array_combine($keys, $array);
}



function compare($stringa, $stringb)
{
	$arraya = explode(PHP_EOL, $stringa);
	$arrayb = explode(PHP_EOL, $stringb);

	$lnb = -1;
	$commonlines = [];
	foreach ($arraya as $na => $linea) {
		$found = false;
		foreach ($arrayb as $nb => $lineb) {
			if($linea === $lineb && $nb > $lnb && !$found && !empty($linea)) {
				$commonlines[$na] = $nb;
				$merge[] = $arrayb[$nb];
				$lnb = $nb;
				$found = true;
			}
		}
	}


	$merge = [];
	$lnb = 0;
	foreach ($arraya as $na => $linea) {
		if(array_key_exists($na, $commonlines)) {
			for ($j=$lnb; $j <= $commonlines[$na]; $j++) { 
					$merge[] = $arrayb[$j];
			}
			$lnb = $j;
		} else {
			$merge[] = $arraya[$na];
		}
	}
	for ($k=$lnb; ; $k++) { 
		if(array_key_exists($k, $arrayb)) {
			$merge[] = $arrayb[$k];
		} else {
			break;
		}
	}

	return implode(PHP_EOL, $merge);
}




?>