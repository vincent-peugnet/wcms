<?php

use Http\Discovery\Exception\NotFoundException;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Wcms\Chmodexception;
use Wcms\Ioexception;
use Wcms\Mediaopt;
use Wcms\Model;

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

/**
 * Clean string from characters outside `[0-9a-z-_]` and troncate it
 * @param string $input
 * @param int $max minmum input length to trucate id
 * @return string output formated id
 * @todo transfert as Model function and store Regex as const
 */
function idclean(string $input, int $max = Wcms\Model::MAX_ID_LENGTH): string
{
    $regex = '%[^a-z0-9-_]%';
    $input = urldecode($input);
    $input = strip_tags($input);

    if (preg_match($regex, $input)) {
        $search =  ['é', 'à', 'è', 'ç', 'ù', 'ü', 'ï', 'î', ' '];
        $replace = ['e', 'a', 'e', 'c', 'u', 'u', 'i', 'i', '-'];
        $input = str_replace($search, $replace, $input);

        $input = preg_replace($regex, '', strtolower(trim($input)));

        $input = substr($input, 0, $max);
    }
    return $input;
}

/**
 * @return bool true if valid ID otherwise false
 * @todo transfert to Model function and use same Regex as idclean
 */
function idcheck(string $id): bool
{
    $regex = '%[^a-z0-9-_]%';
    return !(bool) (preg_match($regex, $id));
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
        $folder = '├─📂<span id="currentdir">' . $dirname . '<span>';
    } else {
        $folder = '├─📁' . $dirname;
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
        $folder = '├─📂<span id="currentdir">' . $dirname . '<span>';
        $checked = 'checked';
    } else {
        $folder = '├─📁' . $dirname;
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
