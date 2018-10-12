<?php
function bddconnect($host, $bdname, $user, $password)
{
	try {
		$bdd = new PDO('mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8', $user, $password);
	} catch (Exeption $e) {
		die('Erreur : ' . $e->getMessage());
	}
	return $bdd;
}

function my_autoloader($class)
{
    require('../../class/class.w.' . strtolower($class) . '.php');
}

function secure()
{
	if (!isset($_SESSION['id'])) {
		header("location: ./");
	}
}

function head($title)
{
	?>
	<head>
		<meta charset="utf8" />
		<meta name="viexport" content="width=device-width" />
		<link href="./css/style.css" rel="stylesheet" />
		<title><?= $title ?></title>
	</head>
	<?php

}

function search($haystack, $debut, $fin)
{
	$list = [];

	$indexdebut = strpos($haystack, $debut);
	if ($indexdebut !== false) {
		$indexdebut += strlen($debut);
		$indexfin = strpos($haystack, $fin, $indexdebut);
		if ($indexfin !== false) {
			array_push($list, substr($haystack, $indexdebut, $indexfin - $indexdebut));
			$haystack = substr($haystack, $indexfin);
			$list = array_merge($list, search($haystack, $debut, $fin));
		}
	}
	return $list;

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


?>