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
		<link href="/css/style.css" rel="stylesheet" />
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

// Retrieve JPEG width and height without downloading/reading entire image.
function getjpegsize($img_loc) {
    $handle = fopen($img_loc, "rb") or die("Invalid file stream.");
    $new_block = NULL;
    if(!feof($handle)) {
        $new_block = fread($handle, 32);
        $i = 0;
        if($new_block[$i]=="\xFF" && $new_block[$i+1]=="\xD8" && $new_block[$i+2]=="\xFF" && $new_block[$i+3]=="\xE0") {
            $i += 4;
            if($new_block[$i+2]=="\x4A" && $new_block[$i+3]=="\x46" && $new_block[$i+4]=="\x49" && $new_block[$i+5]=="\x46" && $new_block[$i+6]=="\x00") {
                // Read block size and skip ahead to begin cycling through blocks in search of SOF marker
                $block_size = unpack("H*", $new_block[$i] . $new_block[$i+1]);
                $block_size = hexdec($block_size[1]);
                while(!feof($handle)) {
                    $i += $block_size;
                    $new_block .= fread($handle, $block_size);
                    if($new_block[$i]=="\xFF") {
                        // New block detected, check for SOF marker
                        $sof_marker = array("\xC0", "\xC1", "\xC2", "\xC3", "\xC5", "\xC6", "\xC7", "\xC8", "\xC9", "\xCA", "\xCB", "\xCD", "\xCE", "\xCF");
                        if(in_array($new_block[$i+1], $sof_marker)) {
                            // SOF marker detected. Width and height information is contained in bytes 4-7 after this byte.
                            $size_data = $new_block[$i+2] . $new_block[$i+3] . $new_block[$i+4] . $new_block[$i+5] . $new_block[$i+6] . $new_block[$i+7] . $new_block[$i+8];
                            $unpacked = unpack("H*", $size_data);
                            $unpacked = $unpacked[1];
                            $height = hexdec($unpacked[6] . $unpacked[7] . $unpacked[8] . $unpacked[9]);
                            $width = hexdec($unpacked[10] . $unpacked[11] . $unpacked[12] . $unpacked[13]);
                            return array($width, $height);
                        } else {
                            // Skip block marker and read block size
                            $i += 2;
                            $block_size = unpack("H*", $new_block[$i] . $new_block[$i+1]);
                            $block_size = hexdec($block_size[1]);
                        }
                    } else {
                        return FALSE;
                    }
                }
            }
        }
    }
    return FALSE;
}



?>