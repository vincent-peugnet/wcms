<?php

use Wcms\Medialist;

use function Clue\StreamFilter\fun;

function readablesize($bytes, $base = 2 ** 10)
{
	$format = ' %d %s';

	if ($base === 2 ** 10) {
		$i = 'i';
	} else {
		$i = '';
	}

	$unit = '';

	if ($bytes < $base) {
		$num = $bytes;
	} elseif ($bytes < $base ** 2) {
		$num = round($bytes / $base, 1);
		$unit = 'K' . $i;
	} elseif ($bytes < $base ** 3) {
		$num = round($bytes / $base ** 2, 1);
		$unit = 'M' . $i;
	} elseif ($bytes < $base ** 4) {
		$num = round($bytes / $base ** 3, 1);
		$unit = 'G' . $i;
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

	$input = substr($input, 0, Wcms\Model::MAX_ID_LENGTH);

	return $input;
}

function isreportingerrors()
{
	return function_exists('Sentry\init') && !empty(Wcms\Config::sentrydsn());
}


function getversion()
{
	if(file_exists('VERSION')) {
		$version = trim(file_get_contents('VERSION'));
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



function findsize($file)
{
    if(substr(PHP_OS, 0, 3) == "WIN")
    {
        exec('for %I in ("'.$file.'") do @echo %~zI', $output);
        $return = $output[0];
    }
    else
    {
        $return = filesize($file);
    }
    return $return;
}

function array_diff_assoc_recursive($array1, $array2) {
    $difference=array();
    foreach($array1 as $key => $value) {
        if( is_array($value) ) {
            if( !isset($array2[$key]) || !is_array($array2[$key]) ) {
                $difference[$key] = $value;
            } else {
                $new_diff = array_diff_assoc_recursive($value, $array2[$key]);
                if( !empty($new_diff) )
                    $difference[$key] = $new_diff;
            }
        } else if( !array_key_exists($key,$array2) || $array2[$key] !== $value ) {
            $difference[$key] = $value;
        }
    }
    return $difference;
}


/**
 * Generate a clickable folder tree based on reccurive array
 */
function treecount(array $dirlist, string $dirname, int $deepness, string $path, string $currentdir, Medialist $mediaopt)
{
	if ($path . '/' === $currentdir) {
		$folder = '├─📂<span id="currentdir">' . $dirname . '<span>';
	} else {
		$folder = '├─📁' . $dirname;
	}
	echo '<tr>';
	echo '<td><a href="' . $mediaopt->getpathadress($path) . '">' . str_repeat('&nbsp;&nbsp;', $deepness) . $folder . '</a></td>';
	echo '<td>' . $dirlist['dirfilecount'] . '</td>';
	echo '</tr>';
	foreach ($dirlist as $key => $value) {
		if (is_array($value)) {
			treecount($value, $key, $deepness + 1, $path . DIRECTORY_SEPARATOR . $key, $currentdir, $mediaopt);
		}
	}
}


/**
 * Generate a clickable folder tree based on reccurive array
 */
function basictree(array $dirlist, string $dirname, int $deepness, string $path, string $currentdir)
{

	if ($path === $currentdir) {
		$folder = '├─📂<span id="currentdir">' . $dirname . '<span>';
		$checked = 'checked';
	} else {
		$folder = '├─📁' . $dirname;
		$checked = '';
	}

	if($deepness === 1) {
		$radio = '<input type="radio" name="pagetable" value="' . $dirname . '" id="db_' . $path . '" ' . $checked . '>';
	} else {
		$radio = '';
	}

	echo '<tr>';
	echo '<td>' . $radio . '</td>';
	echo '<td><label for="db_' . $path . '">' . str_repeat('&nbsp;&nbsp;', $deepness) . $folder . '</label></td>';
	echo '<td>' . $dirlist['dirfilecount'] . '</td>';
	echo '</tr>';
	foreach ($dirlist as $key => $value) {
		if (is_array($value)) {
			basictree($value, $key, $deepness + 1, $path . DIRECTORY_SEPARATOR . $key, $currentdir);
		}
	}
}

function checkboxes(string $name, array $optionlist = [], array $checkedlist = [])
{
	$checkboxes = '';
	foreach ($optionlist as $option) {
		$checkboxes .= '<li><input type="checkbox" name="' . $name . '[]" id="' . $option . '" value="' . $option . '"';
		if(in_array($option, $checkedlist)) {
			$checkboxes .= ' checked';
		}
		$checkboxes .= '><label for="' . $option . '">' . $option . '</label></li>';
		$checkboxes .= PHP_EOL;
	}
	return '<ul>' . PHP_EOL . $checkboxes . PHP_EOL . '</ul>';
}


function recurse_copy($src,$dst) { 
    $dir = opendir($src); 
    mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                recurse_copy($src . '/' . $file,$dst . '/' . $file); 
            } 
            else { 
                copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
}

/**
 * Generate a list of <options> html drop down list
 * 
 * @param array $options as `value => title`
 * @param string|int $selected value of actualy selected option
 * 
 * @return string HTML list of options
 */
function options(array $options, $selected = null) : string
{
	$html = '';
	foreach ($options as $value => $title) {
		if($value == $selected) {
			$attribute = 'selected';
		} else {
			$attribute = '';
		}
		$html .= '<option value="' . $value . '" ' . $attribute . '>' . $title . '</option>' . PHP_EOL;
	}
	return $html;
}





?>