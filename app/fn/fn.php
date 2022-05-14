<?php

use Http\Discovery\Exception\NotFoundException;
use Wcms\Chmodexception;
use Wcms\Ioexception;
use Wcms\Mediaopt;

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
    } else {
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
    if ($diff->y > 1) {
        return $str . $diff->y . ' years';
    }
    if ($diff->y == 1) {
        return $str . ' 1 year and ' . $diff->m . ' months';
    }
    if ($diff->m > 1) {
        return $str . $diff->m . ' months';
    }
    if ($diff->m == 1) {
        return $str . ' 1 month and ' . $diff->d . ($diff->d > 1 ? ' days' : ' day');
    }
    if ($diff->d > 1) {
        return $str . $diff->d . ' days';
    }
    if ($diff->d == 1) {
        return $str . ' 1 day and ' . $diff->h . ($diff->h > 1 ? ' hours' : ' hour');
    }
    if ($diff->h > 1) {
        return $str . $diff->h . ' hours';
    }
    if ($diff->h == 1) {
        return $str . ' 1 hour and ' . $diff->i . ($diff->i > 1 ? ' minutes' : ' minute');
    }
    if ($diff->i > 1) {
        return $str . $diff->i . ' minutes';
    }
    if ($diff->i == 1) {
        return $str . ' 1 minute';
    }
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

function isreportingerrors()
{
    return function_exists('Sentry\init') && !empty(Wcms\Config::sentrydsn());
}


function getversion()
{
    if (file_exists('VERSION')) {
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
    if (!array_key_exists($oldkey, $array)) {
        return $array;
    }

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
            if ($linea === $lineb && $nb > $lnb && !$found && !empty($linea)) {
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
        if (array_key_exists($na, $commonlines)) {
            for ($j = $lnb; $j <= $commonlines[$na]; $j++) {
                    $merge[] = $arrayb[$j];
            }
            $lnb = $j;
        } else {
            $merge[] = $arraya[$na];
        }
    }
    for ($k = $lnb;; $k++) {
        if (array_key_exists($k, $arrayb)) {
            $merge[] = $arrayb[$k];
        } else {
            break;
        }
    }

    return implode(PHP_EOL, $merge);
}



function findsize($file)
{
    if (substr(PHP_OS, 0, 3) == "WIN") {
        exec('for %I in ("' . $file . '") do @echo %~zI', $output);
        $return = $output[0];
    } else {
        $return = filesize($file);
    }
    return $return;
}

function array_diff_assoc_recursive($array1, $array2)
{
    $difference = array();
    foreach ($array1 as $key => $value) {
        if (is_array($value)) {
            if (!isset($array2[$key]) || !is_array($array2[$key])) {
                $difference[$key] = $value;
            } else {
                $new_diff = array_diff_assoc_recursive($value, $array2[$key]);
                if (!empty($new_diff)) {
                    $difference[$key] = $new_diff;
                }
            }
        } elseif (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
            $difference[$key] = $value;
        }
    }
    return $difference;
}


/**
 * Generate a clickable folder tree based on reccurive array
 */
function treecount(
    array $dirlist,
    string $dirname,
    int $deepness,
    string $path,
    string $currentdir,
    Mediaopt $mediaopt
) {
    if ($path . '/' === $currentdir) {
        $folder = '├─<i class="fa fa-folder-open-o"></i> <span id="currentdir">' . $dirname . '<span>';
    } else {
        $folder = '├─<i class="fa fa-folder-o"></i> ' . $dirname;
    }
    echo '<tr>';
    $href = $mediaopt->getpathadress($path);
    $foldername = str_repeat('&nbsp;&nbsp;', $deepness) . $folder;
    echo '<td><a href="' . $href . '">' . $foldername . '</a></td>';
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
        $folder = '├─<i class="fa fa-folder-open-o"></i> <span id="currentdir">' . $dirname . '<span>';
        $checked = 'checked';
    } else {
        $folder = '├─<i class="fa fa-folder-o"></i> ' . $dirname;
        $checked = '';
    }

    if ($deepness === 1) {
        // phpcs:ignore Generic.Files.LineLength.TooLong
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
        if (in_array($option, $checkedlist)) {
            $checkboxes .= ' checked';
        }
        $checkboxes .= '><label for="' . $option . '">' . $option . '</label></li>';
        $checkboxes .= PHP_EOL;
    }
    return '<ul>' . PHP_EOL . $checkboxes . PHP_EOL . '</ul>';
}


function recurse_copy($src, $dst)
{
    $dir = opendir($src);
    mkdir($dst);
    while (false !== ( $file = readdir($dir))) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
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
 * @param bool $title Use title as value. Default : false
 *
 * @return string HTML list of options
 */
function options(array $options, $selected = null, $title = false): string
{
    $html = '';
    foreach ($options as $value => $title) {
        if ($value === $selected) {
            $attribute = 'selected';
        } else {
            $attribute = '';
        }
        if ($title) {
            $value = $title;
        }
        $html .= '<option value="' . $value . '" ' . $attribute . '>' . $title . '</option>' . PHP_EOL;
    }
    return $html;
}


/**
 * Hash a Token using secret key and sha256
 *
 * @param string $token Input token
 *
 * @return string Hashed mac
 */
function secrethash(string $token): string
{
    return hash_hmac('sha256', $token, Wcms\Config::secretkey());
}


/**
 * Check if dir exist. If not, create it
 *
 * @param string $dir Directory to check
 * @param bool $createdir create dir in case of non existence default is true
 * @return bool return true if the dir already exist or was created succesfullt. Otherwise return false
 * @throws \InvalidArgumentException If folder creation is impossible or if directory doeas not exist
 */
function dircheck(string $dir, bool $createdir = true): bool
{
    if (!is_dir($dir)) {
        if ($createdir) {
            $parent = dirname($dir);
            if (dircheck($parent)) {
                if (mkdir($dir)) {
                    return true;
                } else {
                    throw new \InvalidArgumentException("Cannot create directory : $dir");
                }
            } else {
                return false;
            }
        } else {
            throw new \InvalidArgumentException("Directory '$dir' does not exist.");
        }
    } else {
        return true;
    }
}


/**
 * Check if a file is accessible or can be writen
 * @param string $path file path to check
 * @param bool $createdir create directory if does not exist
 * @return bool If no error occured
 * @throws \InvalidArgumentException if :
 * parent directory does not exist | is not writable | file exist and not writable
 */
function accessfile(string $path, bool $createdir = false): bool
{
    $dir = dirname($path);
    if (dircheck($dir, $createdir)) {
        if (!is_writable($dir)) {
            throw new \InvalidArgumentException("Directory '$dir' is not writable.");
        }
        if (is_file($path) && !is_writable($path)) {
            throw new \InvalidArgumentException("The file '$path' is not writable.");
        }
        return true;
    } else {
        return false;
    }
}

// Returns a file size limit in bytes based on the PHP upload_max_filesize
// and post_max_size
function file_upload_max_size()
{
    static $max_size = -1;

    if ($max_size < 0) {
      // Start with post_max_size.
        $post_max_size = parse_size(ini_get('post_max_size'));
        if ($post_max_size > 0) {
            $max_size = $post_max_size;
        }

      // If upload_max_size is less, then reduce. Except if upload_max_size is
      // zero, which indicates no limit.
        $upload_max = parse_size(ini_get('upload_max_filesize'));
        if ($upload_max > 0 && $upload_max < $max_size) {
            $max_size = $upload_max;
        }
    }
    return $max_size;
}

function parse_size($size)
{
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
    $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
    $size = floatval($size);
    if ($unit) {
      // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    } else {
        return round($size);
    }
}

/**
 * @param string $filename Path to the file where to write the data.
 * @param mixed $data The data to write. Can be either a string, an array or a stream resource.
 * @param int $permissions in octal value
 *
 * @throws Ioexception when file_put_contents fails
 * @throws Chmodexception when chmod fails
 */
function file_put_content_chmod(string $filename, $data, int $permissions): int
{
    $create = !file_exists($filename);
    $length = file_put_contents($filename, $data);
    if ($length === false) {
        throw new Ioexception("Error while writing $filename");
    }
    if ($create) {
        if ($permissions < 0600 || $permissions > 0777) {
            throw new Chmodexception("Incorrect permissions value", $permissions);
        }
        if (!chmod($filename, $permissions)) {
            throw new Chmodexception("Error while setting file permissions $filename", $permissions);
        }
    }
    return $length;
}

function flatten(array $array): array
{
    $return = array();
    array_walk_recursive($array, function ($a) use (&$return) {
        $return[] = $a;
    });
    return $return;
}

function randombytes(int $seed): string
{
    try {
        return random_bytes($seed);
    } catch (Exception $e) {
        throw new \LogicException("random_bytes failed", 0, $e);
    }
}

/**
 * @param string $url
 * @throws ErrorException if Curl is not installed
 * @throws RuntimeException if curl_exec fail
 * @return string output data
 */
function curl_download(string $url): string
{
    if (!extension_loaded('curl')) {
        throw new ErrorException("Curl extension is not installed");
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    // Include header in result? (0 = yes, 1 = no)
    curl_setopt($ch, CURLOPT_HEADER, 0);
    // Should cURL return or print out the data? (true = return, false = print)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Timeout in seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    // Download the given URL, and return output
    $output = curl_exec($ch);
    // Close the cURL resource, and free system resources
    curl_close($ch);

    if (is_bool($output)) {
        throw new RuntimeException("CURL error");
    } else {
        return $output;
    }
}



/**
 * @param string $input string to be checked
 * @return string first occurence of url in input string
 * @throws NotFoundException when no string is founded
 */
function getfirsturl(string $input): string
{
    if (preg_match('%https?:\/\/\S*%', $input, $out)) {
        return $out[0];
    } else {
        throw new NotFoundException("no url in string: $input");
    }
}

/**
 * @param string $code              This code will be uppercase
 * @return string                   Emoji flag code
 * @throws NotFoundException        When Country code is note listed
 */
function countryflag(string $code): string
{
    // ISO country code source: https://en.wikipedia.org/wiki/ISO_3166-1
    // Emoji unicode codes: http://unicode.org/emoji/charts/full-emoji-list.html#country-flag

    // An array to hold all the countries
    $emoji_flags = array();

    // Now, all the country flags as emojis
    $emoji_flags["AD"] = "\u{1F1E6}\u{1F1E9}";
    $emoji_flags["AE"] = "\u{1F1E6}\u{1F1EA}";
    $emoji_flags["AF"] = "\u{1F1E6}\u{1F1EB}";
    $emoji_flags["AG"] = "\u{1F1E6}\u{1F1EC}";
    $emoji_flags["AI"] = "\u{1F1E6}\u{1F1EE}";
    $emoji_flags["AL"] = "\u{1F1E6}\u{1F1F1}";
    $emoji_flags["AM"] = "\u{1F1E6}\u{1F1F2}";
    $emoji_flags["AO"] = "\u{1F1E6}\u{1F1F4}";
    $emoji_flags["AQ"] = "\u{1F1E6}\u{1F1F6}";
    $emoji_flags["AR"] = "\u{1F1E6}\u{1F1F7}";
    $emoji_flags["AS"] = "\u{1F1E6}\u{1F1F8}";
    $emoji_flags["AT"] = "\u{1F1E6}\u{1F1F9}";
    $emoji_flags["AU"] = "\u{1F1E6}\u{1F1FA}";
    $emoji_flags["AW"] = "\u{1F1E6}\u{1F1FC}";
    $emoji_flags["AX"] = "\u{1F1E6}\u{1F1FD}";
    $emoji_flags["AZ"] = "\u{1F1E6}\u{1F1FF}";
    $emoji_flags["BA"] = "\u{1F1E7}\u{1F1E6}";
    $emoji_flags["BB"] = "\u{1F1E7}\u{1F1E7}";
    $emoji_flags["BD"] = "\u{1F1E7}\u{1F1E9}";
    $emoji_flags["BE"] = "\u{1F1E7}\u{1F1EA}";
    $emoji_flags["BF"] = "\u{1F1E7}\u{1F1EB}";
    $emoji_flags["BG"] = "\u{1F1E7}\u{1F1EC}";
    $emoji_flags["BH"] = "\u{1F1E7}\u{1F1ED}";
    $emoji_flags["BI"] = "\u{1F1E7}\u{1F1EE}";
    $emoji_flags["BJ"] = "\u{1F1E7}\u{1F1EF}";
    $emoji_flags["BL"] = "\u{1F1E7}\u{1F1F1}";
    $emoji_flags["BM"] = "\u{1F1E7}\u{1F1F2}";
    $emoji_flags["BN"] = "\u{1F1E7}\u{1F1F3}";
    $emoji_flags["BO"] = "\u{1F1E7}\u{1F1F4}";
    $emoji_flags["BQ"] = "\u{1F1E7}\u{1F1F6}";
    $emoji_flags["BR"] = "\u{1F1E7}\u{1F1F7}";
    $emoji_flags["BS"] = "\u{1F1E7}\u{1F1F8}";
    $emoji_flags["BT"] = "\u{1F1E7}\u{1F1F9}";
    $emoji_flags["BV"] = "\u{1F1E7}\u{1F1FB}";
    $emoji_flags["BW"] = "\u{1F1E7}\u{1F1FC}";
    $emoji_flags["BY"] = "\u{1F1E7}\u{1F1FE}";
    $emoji_flags["BZ"] = "\u{1F1E7}\u{1F1FF}";
    $emoji_flags["CA"] = "\u{1F1E8}\u{1F1E6}";
    $emoji_flags["CC"] = "\u{1F1E8}\u{1F1E8}";
    $emoji_flags["CD"] = "\u{1F1E8}\u{1F1E9}";
    $emoji_flags["CF"] = "\u{1F1E8}\u{1F1EB}";
    $emoji_flags["CG"] = "\u{1F1E8}\u{1F1EC}";
    $emoji_flags["CH"] = "\u{1F1E8}\u{1F1ED}";
    $emoji_flags["CI"] = "\u{1F1E8}\u{1F1EE}";
    $emoji_flags["CK"] = "\u{1F1E8}\u{1F1F0}";
    $emoji_flags["CL"] = "\u{1F1E8}\u{1F1F1}";
    $emoji_flags["CM"] = "\u{1F1E8}\u{1F1F2}";
    $emoji_flags["CN"] = "\u{1F1E8}\u{1F1F3}";
    $emoji_flags["CO"] = "\u{1F1E8}\u{1F1F4}";
    $emoji_flags["CR"] = "\u{1F1E8}\u{1F1F7}";
    $emoji_flags["CU"] = "\u{1F1E8}\u{1F1FA}";
    $emoji_flags["CV"] = "\u{1F1E8}\u{1F1FB}";
    $emoji_flags["CW"] = "\u{1F1E8}\u{1F1FC}";
    $emoji_flags["CX"] = "\u{1F1E8}\u{1F1FD}";
    $emoji_flags["CY"] = "\u{1F1E8}\u{1F1FE}";
    $emoji_flags["CZ"] = "\u{1F1E8}\u{1F1FF}";
    $emoji_flags["DE"] = "\u{1F1E9}\u{1F1EA}";
    $emoji_flags["DG"] = "\u{1F1E9}\u{1F1EC}";
    $emoji_flags["DJ"] = "\u{1F1E9}\u{1F1EF}";
    $emoji_flags["DK"] = "\u{1F1E9}\u{1F1F0}";
    $emoji_flags["DM"] = "\u{1F1E9}\u{1F1F2}";
    $emoji_flags["DO"] = "\u{1F1E9}\u{1F1F4}";
    $emoji_flags["DZ"] = "\u{1F1E9}\u{1F1FF}";
    $emoji_flags["EC"] = "\u{1F1EA}\u{1F1E8}";
    $emoji_flags["EE"] = "\u{1F1EA}\u{1F1EA}";
    $emoji_flags["EG"] = "\u{1F1EA}\u{1F1EC}";
    $emoji_flags["EH"] = "\u{1F1EA}\u{1F1ED}";
    $emoji_flags["ER"] = "\u{1F1EA}\u{1F1F7}";
    $emoji_flags["ES"] = "\u{1F1EA}\u{1F1F8}";
    $emoji_flags["ET"] = "\u{1F1EA}\u{1F1F9}";
    $emoji_flags["FI"] = "\u{1F1EB}\u{1F1EE}";
    $emoji_flags["FJ"] = "\u{1F1EB}\u{1F1EF}";
    $emoji_flags["FK"] = "\u{1F1EB}\u{1F1F0}";
    $emoji_flags["FM"] = "\u{1F1EB}\u{1F1F2}";
    $emoji_flags["FO"] = "\u{1F1EB}\u{1F1F4}";
    $emoji_flags["FR"] = "\u{1F1EB}\u{1F1F7}";
    $emoji_flags["GA"] = "\u{1F1EC}\u{1F1E6}";
    $emoji_flags["GB"] = "\u{1F1EC}\u{1F1E7}";
    $emoji_flags["GD"] = "\u{1F1EC}\u{1F1E9}";
    $emoji_flags["GE"] = "\u{1F1EC}\u{1F1EA}";
    $emoji_flags["GF"] = "\u{1F1EC}\u{1F1EB}";
    $emoji_flags["GG"] = "\u{1F1EC}\u{1F1EC}";
    $emoji_flags["GH"] = "\u{1F1EC}\u{1F1ED}";
    $emoji_flags["GI"] = "\u{1F1EC}\u{1F1EE}";
    $emoji_flags["GL"] = "\u{1F1EC}\u{1F1F1}";
    $emoji_flags["GM"] = "\u{1F1EC}\u{1F1F2}";
    $emoji_flags["GN"] = "\u{1F1EC}\u{1F1F3}";
    $emoji_flags["GP"] = "\u{1F1EC}\u{1F1F5}";
    $emoji_flags["GQ"] = "\u{1F1EC}\u{1F1F6}";
    $emoji_flags["GR"] = "\u{1F1EC}\u{1F1F7}";
    $emoji_flags["GS"] = "\u{1F1EC}\u{1F1F8}";
    $emoji_flags["GT"] = "\u{1F1EC}\u{1F1F9}";
    $emoji_flags["GU"] = "\u{1F1EC}\u{1F1FA}";
    $emoji_flags["GW"] = "\u{1F1EC}\u{1F1FC}";
    $emoji_flags["GY"] = "\u{1F1EC}\u{1F1FE}";
    $emoji_flags["HK"] = "\u{1F1ED}\u{1F1F0}";
    $emoji_flags["HM"] = "\u{1F1ED}\u{1F1F2}";
    $emoji_flags["HN"] = "\u{1F1ED}\u{1F1F3}";
    $emoji_flags["HR"] = "\u{1F1ED}\u{1F1F7}";
    $emoji_flags["HT"] = "\u{1F1ED}\u{1F1F9}";
    $emoji_flags["HU"] = "\u{1F1ED}\u{1F1FA}";
    $emoji_flags["ID"] = "\u{1F1EE}\u{1F1E9}";
    $emoji_flags["IE"] = "\u{1F1EE}\u{1F1EA}";
    $emoji_flags["IL"] = "\u{1F1EE}\u{1F1F1}";
    $emoji_flags["IM"] = "\u{1F1EE}\u{1F1F2}";
    $emoji_flags["IN"] = "\u{1F1EE}\u{1F1F3}";
    $emoji_flags["IO"] = "\u{1F1EE}\u{1F1F4}";
    $emoji_flags["IQ"] = "\u{1F1EE}\u{1F1F6}";
    $emoji_flags["IR"] = "\u{1F1EE}\u{1F1F7}";
    $emoji_flags["IS"] = "\u{1F1EE}\u{1F1F8}";
    $emoji_flags["IT"] = "\u{1F1EE}\u{1F1F9}";
    $emoji_flags["JE"] = "\u{1F1EF}\u{1F1EA}";
    $emoji_flags["JM"] = "\u{1F1EF}\u{1F1F2}";
    $emoji_flags["JO"] = "\u{1F1EF}\u{1F1F4}";
    $emoji_flags["JP"] = "\u{1F1EF}\u{1F1F5}";
    $emoji_flags["KE"] = "\u{1F1F0}\u{1F1EA}";
    $emoji_flags["KG"] = "\u{1F1F0}\u{1F1EC}";
    $emoji_flags["KH"] = "\u{1F1F0}\u{1F1ED}";
    $emoji_flags["KI"] = "\u{1F1F0}\u{1F1EE}";
    $emoji_flags["KM"] = "\u{1F1F0}\u{1F1F2}";
    $emoji_flags["KN"] = "\u{1F1F0}\u{1F1F3}";
    $emoji_flags["KP"] = "\u{1F1F0}\u{1F1F5}";
    $emoji_flags["KR"] = "\u{1F1F0}\u{1F1F7}";
    $emoji_flags["KW"] = "\u{1F1F0}\u{1F1FC}";
    $emoji_flags["KY"] = "\u{1F1F0}\u{1F1FE}";
    $emoji_flags["KZ"] = "\u{1F1F0}\u{1F1FF}";
    $emoji_flags["LA"] = "\u{1F1F1}\u{1F1E6}";
    $emoji_flags["LB"] = "\u{1F1F1}\u{1F1E7}";
    $emoji_flags["LC"] = "\u{1F1F1}\u{1F1E8}";
    $emoji_flags["LI"] = "\u{1F1F1}\u{1F1EE}";
    $emoji_flags["LK"] = "\u{1F1F1}\u{1F1F0}";
    $emoji_flags["LR"] = "\u{1F1F1}\u{1F1F7}";
    $emoji_flags["LS"] = "\u{1F1F1}\u{1F1F8}";
    $emoji_flags["LT"] = "\u{1F1F1}\u{1F1F9}";
    $emoji_flags["LU"] = "\u{1F1F1}\u{1F1FA}";
    $emoji_flags["LV"] = "\u{1F1F1}\u{1F1FB}";
    $emoji_flags["LY"] = "\u{1F1F1}\u{1F1FE}";
    $emoji_flags["MA"] = "\u{1F1F2}\u{1F1E6}";
    $emoji_flags["MC"] = "\u{1F1F2}\u{1F1E8}";
    $emoji_flags["MD"] = "\u{1F1F2}\u{1F1E9}";
    $emoji_flags["ME"] = "\u{1F1F2}\u{1F1EA}";
    $emoji_flags["MF"] = "\u{1F1F2}\u{1F1EB}";
    $emoji_flags["MG"] = "\u{1F1F2}\u{1F1EC}";
    $emoji_flags["MH"] = "\u{1F1F2}\u{1F1ED}";
    $emoji_flags["MK"] = "\u{1F1F2}\u{1F1F0}";
    $emoji_flags["ML"] = "\u{1F1F2}\u{1F1F1}";
    $emoji_flags["MM"] = "\u{1F1F2}\u{1F1F2}";
    $emoji_flags["MN"] = "\u{1F1F2}\u{1F1F3}";
    $emoji_flags["MO"] = "\u{1F1F2}\u{1F1F4}";
    $emoji_flags["MP"] = "\u{1F1F2}\u{1F1F5}";
    $emoji_flags["MQ"] = "\u{1F1F2}\u{1F1F6}";
    $emoji_flags["MR"] = "\u{1F1F2}\u{1F1F7}";
    $emoji_flags["MS"] = "\u{1F1F2}\u{1F1F8}";
    $emoji_flags["MT"] = "\u{1F1F2}\u{1F1F9}";
    $emoji_flags["MU"] = "\u{1F1F2}\u{1F1FA}";
    $emoji_flags["MV"] = "\u{1F1F2}\u{1F1FB}";
    $emoji_flags["MW"] = "\u{1F1F2}\u{1F1FC}";
    $emoji_flags["MX"] = "\u{1F1F2}\u{1F1FD}";
    $emoji_flags["MY"] = "\u{1F1F2}\u{1F1FE}";
    $emoji_flags["MZ"] = "\u{1F1F2}\u{1F1FF}";
    $emoji_flags["NA"] = "\u{1F1F3}\u{1F1E6}";
    $emoji_flags["NC"] = "\u{1F1F3}\u{1F1E8}";
    $emoji_flags["NE"] = "\u{1F1F3}\u{1F1EA}";
    $emoji_flags["NF"] = "\u{1F1F3}\u{1F1EB}";
    $emoji_flags["NG"] = "\u{1F1F3}\u{1F1EC}";
    $emoji_flags["NI"] = "\u{1F1F3}\u{1F1EE}";
    $emoji_flags["NL"] = "\u{1F1F3}\u{1F1F1}";
    $emoji_flags["NO"] = "\u{1F1F3}\u{1F1F4}";
    $emoji_flags["NP"] = "\u{1F1F3}\u{1F1F5}";
    $emoji_flags["NR"] = "\u{1F1F3}\u{1F1F7}";
    $emoji_flags["NU"] = "\u{1F1F3}\u{1F1FA}";
    $emoji_flags["NZ"] = "\u{1F1F3}\u{1F1FF}";
    $emoji_flags["OM"] = "\u{1F1F4}\u{1F1F2}";
    $emoji_flags["PA"] = "\u{1F1F5}\u{1F1E6}";
    $emoji_flags["PE"] = "\u{1F1F5}\u{1F1EA}";
    $emoji_flags["PF"] = "\u{1F1F5}\u{1F1EB}";
    $emoji_flags["PG"] = "\u{1F1F5}\u{1F1EC}";
    $emoji_flags["PH"] = "\u{1F1F5}\u{1F1ED}";
    $emoji_flags["PK"] = "\u{1F1F5}\u{1F1F0}";
    $emoji_flags["PL"] = "\u{1F1F5}\u{1F1F1}";
    $emoji_flags["PM"] = "\u{1F1F5}\u{1F1F2}";
    $emoji_flags["PN"] = "\u{1F1F5}\u{1F1F3}";
    $emoji_flags["PR"] = "\u{1F1F5}\u{1F1F7}";
    $emoji_flags["PS"] = "\u{1F1F5}\u{1F1F8}";
    $emoji_flags["PT"] = "\u{1F1F5}\u{1F1F9}";
    $emoji_flags["PW"] = "\u{1F1F5}\u{1F1FC}";
    $emoji_flags["PY"] = "\u{1F1F5}\u{1F1FE}";
    $emoji_flags["QA"] = "\u{1F1F6}\u{1F1E6}";
    $emoji_flags["RE"] = "\u{1F1F7}\u{1F1EA}";
    $emoji_flags["RO"] = "\u{1F1F7}\u{1F1F4}";
    $emoji_flags["RS"] = "\u{1F1F7}\u{1F1F8}";
    $emoji_flags["RU"] = "\u{1F1F7}\u{1F1FA}";
    $emoji_flags["RW"] = "\u{1F1F7}\u{1F1FC}";
    $emoji_flags["SA"] = "\u{1F1F8}\u{1F1E6}";
    $emoji_flags["SB"] = "\u{1F1F8}\u{1F1E7}";
    $emoji_flags["SC"] = "\u{1F1F8}\u{1F1E8}";
    $emoji_flags["SD"] = "\u{1F1F8}\u{1F1E9}";
    $emoji_flags["SE"] = "\u{1F1F8}\u{1F1EA}";
    $emoji_flags["SG"] = "\u{1F1F8}\u{1F1EC}";
    $emoji_flags["SH"] = "\u{1F1F8}\u{1F1ED}";
    $emoji_flags["SI"] = "\u{1F1F8}\u{1F1EE}";
    $emoji_flags["SJ"] = "\u{1F1F8}\u{1F1EF}";
    $emoji_flags["SK"] = "\u{1F1F8}\u{1F1F0}";
    $emoji_flags["SL"] = "\u{1F1F8}\u{1F1F1}";
    $emoji_flags["SM"] = "\u{1F1F8}\u{1F1F2}";
    $emoji_flags["SN"] = "\u{1F1F8}\u{1F1F3}";
    $emoji_flags["SO"] = "\u{1F1F8}\u{1F1F4}";
    $emoji_flags["SR"] = "\u{1F1F8}\u{1F1F7}";
    $emoji_flags["SS"] = "\u{1F1F8}\u{1F1F8}";
    $emoji_flags["ST"] = "\u{1F1F8}\u{1F1F9}";
    $emoji_flags["SV"] = "\u{1F1F8}\u{1F1FB}";
    $emoji_flags["SX"] = "\u{1F1F8}\u{1F1FD}";
    $emoji_flags["SY"] = "\u{1F1F8}\u{1F1FE}";
    $emoji_flags["SZ"] = "\u{1F1F8}\u{1F1FF}";
    $emoji_flags["TC"] = "\u{1F1F9}\u{1F1E8}";
    $emoji_flags["TD"] = "\u{1F1F9}\u{1F1E9}";
    $emoji_flags["TF"] = "\u{1F1F9}\u{1F1EB}";
    $emoji_flags["TG"] = "\u{1F1F9}\u{1F1EC}";
    $emoji_flags["TH"] = "\u{1F1F9}\u{1F1ED}";
    $emoji_flags["TJ"] = "\u{1F1F9}\u{1F1EF}";
    $emoji_flags["TK"] = "\u{1F1F9}\u{1F1F0}";
    $emoji_flags["TL"] = "\u{1F1F9}\u{1F1F1}";
    $emoji_flags["TM"] = "\u{1F1F9}\u{1F1F2}";
    $emoji_flags["TN"] = "\u{1F1F9}\u{1F1F3}";
    $emoji_flags["TO"] = "\u{1F1F9}\u{1F1F4}";
    $emoji_flags["TR"] = "\u{1F1F9}\u{1F1F7}";
    $emoji_flags["TT"] = "\u{1F1F9}\u{1F1F9}";
    $emoji_flags["TV"] = "\u{1F1F9}\u{1F1FB}";
    $emoji_flags["TW"] = "\u{1F1F9}\u{1F1FC}";
    $emoji_flags["TZ"] = "\u{1F1F9}\u{1F1FF}";
    $emoji_flags["UA"] = "\u{1F1FA}\u{1F1E6}";
    $emoji_flags["UG"] = "\u{1F1FA}\u{1F1EC}";
    $emoji_flags["UM"] = "\u{1F1FA}\u{1F1F2}";
    $emoji_flags["US"] = "\u{1F1FA}\u{1F1F8}";
    $emoji_flags["UY"] = "\u{1F1FA}\u{1F1FE}";
    $emoji_flags["UZ"] = "\u{1F1FA}\u{1F1FF}";
    $emoji_flags["VA"] = "\u{1F1FB}\u{1F1E6}";
    $emoji_flags["VC"] = "\u{1F1FB}\u{1F1E8}";
    $emoji_flags["VE"] = "\u{1F1FB}\u{1F1EA}";
    $emoji_flags["VG"] = "\u{1F1FB}\u{1F1EC}";
    $emoji_flags["VI"] = "\u{1F1FB}\u{1F1EE}";
    $emoji_flags["VN"] = "\u{1F1FB}\u{1F1F3}";
    $emoji_flags["VU"] = "\u{1F1FB}\u{1F1FA}";
    $emoji_flags["WF"] = "\u{1F1FC}\u{1F1EB}";
    $emoji_flags["WS"] = "\u{1F1FC}\u{1F1F8}";
    $emoji_flags["XK"] = "\u{1F1FD}\u{1F1F0}";
    $emoji_flags["YE"] = "\u{1F1FE}\u{1F1EA}";
    $emoji_flags["YT"] = "\u{1F1FE}\u{1F1F9}";
    $emoji_flags["ZA"] = "\u{1F1FF}\u{1F1E6}";
    $emoji_flags["ZM"] = "\u{1F1FF}\u{1F1F2}";
    $emoji_flags["ZW"] = "\u{1F1FF}\u{1F1FC}";

    // custom
    $emoji_flags["EN"] = $emoji_flags["GB"];

    $code = strtoupper($code);
    $code = substr($code, 0, 2);
    if (key_exists($code, $emoji_flags)) {
        return $emoji_flags[$code];
    } else {
        throw new NotFoundException("Country code not found");
    }
}
