<?php

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


function getversion(): string
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
    $arraya = explode("\n", $stringa);
    $arrayb = explode("\n", $stringb);

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

    return implode("\n", $merge);
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
        $checkboxes .= "><label for=\"$option\">$option</label></li>\n";
    }
    return "<ul>$checkboxes</ul>";
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
        $html .= "<option value=\"$value\" $attribute>$title</option>\n";
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
 * @param string $input                     string to be checked
 * @return string                           first occurence of url in input string
 * @throws RangeException                   when no string is founded
 */
function getfirsturl(string $input): string
{
    if (preg_match('%https?:\/\/\S*%', $input, $out)) {
        return $out[0];
    } else {
        throw new RangeException("no url in string: $input");
    }
}

/**
 * Convert windows encoded new lines to UNIX encoded new lines
 *
 * @param string $text                      text to be converted
 *
 * @return string                           converted text
 */
function crlf2lf(string $text): string
{
    return str_replace("\r\n", "\n", $text);
}
