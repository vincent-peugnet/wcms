<?php

use donatj\UserAgent\Platforms;
use donatj\UserAgent\UserAgent;
use Wcms\Exception\Filesystemexception\Folderexception;
use Wcms\Exception\Missingextensionexception;

const HTTP_STATUS = array(
    100 => 'Continue',
    101 => 'Switching Protocols',
    102 => 'Processing',
    103 => 'Checkpoint',
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    207 => 'Multi-Status',
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    306 => 'Switch Proxy',
    307 => 'Temporary Redirect',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Long',
    415 => 'Unsupported Media Type',
    416 => 'Requested Range Not Satisfiable',
    417 => 'Expectation Failed',
    418 => 'I\'m a teapot',
    422 => 'Unprocessable Entity',
    423 => 'Locked',
    424 => 'Failed Dependency',
    425 => 'Unordered Collection',
    426 => 'Upgrade Required',
    428 => 'Precondition Required',
    429 => 'Too Many Requests',
    431 => 'Request Header Fields Too Large',
    449 => 'Retry With',
    450 => 'Blocked by Windows Parental Controls',
    451 => 'Unavailable For Legal Reasons',
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Timeout',
    505 => 'HTTP Version Not Supported',
    506 => 'Variant Also Negotiates',
    507 => 'Insufficient Storage',
    509 => 'Bandwidth Limit Exceeded',
    510 => 'Not Extended'
);

function readablesize(float $bytes, int $base = 2 ** 10): string
{
    $format = '%d&nbsp;%s';

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

/**
 * Human readable date interval
 *
 * @param DateInterval $diff
 * @return string
 */
function hrdi(DateInterval $diff): string
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

/**
 * Check if Sentry service is enabled
 */
function isreportingerrors(): bool
{
    return function_exists('Sentry\init') && !empty(Wcms\Config::sentrydsn());
}

/**
 * Get W version using VERSION file in root directory.
 * @return string                           W's current version.
 *                                          If the file cannot be read, `unknown` is used as output.
 */
function getversion(): string
{
    if (file_exists('VERSION')) {
        $version = trim(file_get_contents('VERSION'));
    } else {
        $version = 'unknown';
    }
    return $version;
}

/**
 * @param mixed[] $array1
 * @param mixed[] $array2
 * @return mixed[]
 */
function array_diff_assoc_recursive(array $array1, array $array2): array
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
 * Generate a list of <options> html drop down list
 *
 * @param string[] $options                 as `value => title`
 * @param string|int $selected              value of currently selected option
 * @param bool $simple                      Use title as value. Default : `false`
 *
 * @return string                           HTML list of options
 */
function options(array $options, $selected = null, $simple = false): string
{
    $html = '';
    foreach ($options as $value => $title) {
        if ($simple) {
            $value = $title;
        }
        $attribute = $value === $selected ? 'selected' : '';
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
function file_upload_max_size(): float
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

function parse_size(string $size): float
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
 * @param mixed[] $array
 *
 * @return mixed[]
 */
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
        /**
         * PHPStan don't get that RandomException is a child of Exception
         * @phpstan-ignore missingType.checkedException */
        return random_bytes($seed);
    } catch (Exception $e) {
        throw new \LogicException("random_bytes failed", 0, $e);
    }
}

/**
 * @param string $url
 * @return string output data
 *
 * @throws Missingextensionexception if PHP Curl extension is not installed
 * @throws RuntimeException if curl_exec fail
 */
function curl_download(string $url): string
{
    if (!extension_loaded('curl')) {
        throw new Missingextensionexception("PHP Curl extension is not installed");
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
 * @throws RangeException                   when no URL string is founded
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

// source: Laravel Framework
// https://github.com/laravel/framework/blob/8.x/src/Illuminate/Support/Str.php
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool
    {
        return $needle !== '' && mb_substr($haystack, -strlen($needle)) === (string)$needle;
    }
}
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

/**
 * Insert a string after the first occurence of a string. If the string does not exist, nothing is inserted.
 */
function insert_after(string $text, string $after, string $insert): string
{
    $afterpos = strpos($text, $after);
    if ($afterpos === false) {
        return $text;
    }
    $pos = $afterpos + strlen($after);
    return substr_replace($text, $insert, $pos, 0);
}

/**
 * Returns available space on filesystem or disk partition in octets
 *
 * @param string $directory                 Directory to mesure
 * @return float                            In bytes
 *
 * @throws RuntimeException                 In case of fail
 */
function disk_free_space_ex(string $directory): float
{
    $dfs = disk_free_space($directory);
    if (is_bool($dfs)) {
        throw new RuntimeException("Error while calculating free space left on disk with directory `$directory`");
    } else {
        return $dfs;
    }
}

/**
 * Get system tmp dir without trailing slah
 *
 * @return string                           something like `/tmp`
 */
function get_temp_dir()
{
    return rtrim(sys_get_temp_dir(), "/");
}

/**
 * Create a folder with an auto-generated name, in OS temp directory
 *
 * @param string $prefix                    A prefix to suit your case (It is nice to precise that it is related to W)
 * @return string                           Absolute created path without trailing slash
 *
 * @throws Folderexception                  If creation failed
 */
function mktmpdir(string $prefix): string
{
    $tmp = get_temp_dir();
    $randstr = dechex(mt_rand() % (2 << 16));
    $path = "$tmp/$prefix-$randstr";
    if (!mkdir($path)) {
        throw new Folderexception("cannot create tmp dir '$path'");
    }
    return $path;
}

/**
 * List subfoder of a folder
 *
 * @param string $path                  a path to a folder
 *
 * @return string[]                     list of folder names
 *
 * @throws RuntimeException             If given path is not a valid directory
 */
function subfolders(string $path): array
{
    $subfolders = [];
    $path = rtrim($path, '/');
    $files = @scandir($path); // avoid throwing E_WARNING errors
    if ($files === false) {
        throw new RuntimeException("'$path' is not a directory");
    }
    foreach ($files as $file) {
        if (is_dir("$path/$file") && $file !== '.' && $file !== '..') {
            $subfolders[] = $file;
        }
    }
    return $subfolders;
}

/**
 * @param string $path                  a path to a folder
 *
 * @return int                          Number of file inside the folder
 *
 * @throws RuntimeException             If given path is not a valid directory
 */
function filecount($path): int
{
    $path = rtrim($path, '/');
    $files = scandir($path);
    if ($files === false) {
        throw new RuntimeException("'$path' is not a directory");
    }
    $count = 0;
    foreach ($files as $file) {
        if (is_file("$path/$file")) {
            $count++;
        }
    }
    return $count;
}

/**
 * Determine if visitor use a mobile based on User Agent.
 */
function is_mobile(UserAgent $ua): bool
{
    $mobileplatforms = [
        Platforms::ANDROID,
        Platforms::BLACKBERRY,
        Platforms::IPHONE,
        Platforms::IPOD,
    ];

    return in_array($ua->platform(), $mobileplatforms);
}

/**
 * @param mixed $image
 *
 * @see https://stackoverflow.com/a/13963783
 *
 * @todo update $image var type when dropping PHP7.4 support *
 */
function image_fix_orientation_gd(&$image, string $filename): void
{
    $exif = exif_read_data($filename);

    if (!empty($exif['Orientation'])) {
        switch ($exif['Orientation']) {
            case 3:
                $image = imagerotate($image, 180, 0);
                break;

            case 6:
                $image = imagerotate($image, -90, 0);
                break;

            case 8:
                $image = imagerotate($image, 90, 0);
                break;
        }
    }
}

/**
 * @throws ImagickException                 in case of Imagick errors
 *
 * @see https://stackoverflow.com/a/13963783
 */
function image_fix_orientation_imagick(Imagick $image): void
{
    if (method_exists($image, 'getImageProperty')) {
        $orientation = $image->getImageProperty('exif:Orientation');
    } else {
        $filename = $image->getImageFilename();

        if (empty($filename)) {
            $filename = 'data://image/jpeg;base64,' . base64_encode($image->getImageBlob());
        }

        $exif = exif_read_data($filename);
        $orientation = isset($exif['Orientation']) ? $exif['Orientation'] : null;
    }

    if (!empty($orientation)) {
        switch ($orientation) {
            case 3:
                $image->rotateImage('#000000', 180);
                break;

            case 6:
                $image->rotateImage('#000000', 90);
                break;

            case 8:
                $image->rotateImage('#000000', -90);
                break;
        }
    }
}
