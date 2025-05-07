<?php

namespace Wcms;

use RuntimeException;
use Wcms\Exception\Filesystemexception;
use Wcms\Exception\Missingextensionexception;

/**
 * Check URL
 */
class Serviceurlchecker
{
    /** @var array[] $urls cached URLs */
    protected array $urls = [];

    /** @var string[] $queue ULRs that need to be checked */
    protected array $queue = [];

    /** @var int $timeout before stopping Web check (in seconds) */
    protected int $timeout;

    /** @var bool $cacheonly Limit URL checking to cache */
    protected bool $cacheonly = false;

    /** @var int MAX_BOUNCE limit of redirections to follow */
    public const MAX_BOUNCE = 8;

    /** @var null[] URL response code considered as not dead */
    public const ACCEPTED_RESPONSE_CODES = [
        200 => null,
        401 => null,
        403 => null,
        405 => null,
    ];

    /**
     * Tool that check for urls status, first in the cache, then on the Web
     * A timeout have to be set to limit Web checking time
     *
     * @param int $timeout                  allocated time for looking URL on the Web (in seconds)
     *                                      if set to `0`, Check on the Web is disabled: only the cache is used
     */
    public function __construct(int $timeout = 0)
    {
        $this->timeout = $timeout;
        if ($timeout === 0) {
            $this->cacheonly = true;
        }
        try {
            $urlfile = Fs::readfile(Model::URLS_FILE);
            $this->urls = json_decode($urlfile, true);
        } catch (Filesystemexception $e) {
            // This mean the url cache file does not exist
        }
    }

    /**
     * Check status of URL
     * If the URL status is not cached and valid, it's added to the queue.
     *
     * @param string $url                   The URL to verify
     *
     * @return bool                         True if the url is alive, false if it's dead
     *
     * @throws RuntimeException             If the status of the URL is not cached
     */
    public function check(string $url): bool
    {
        if ($this->iscachedandvalid($url)) {
            return $this->responseisaccepted($this->urls[$url]['response']);
        } elseif (!$this->cacheonly) {
            $this->queue[] = $url;
        }
        throw new RuntimeException('no status about this URL');
    }

    /**
     * Check if the status of URL is cached and has not expired
     * If cache is expired, the entry is deleted
     *
     * @param string $url                   The URL to verify
     *
     * @return bool                         Indicate if the URL status is cached and has not expired
     */
    protected function iscachedandvalid(string $url): bool
    {
        if (!key_exists($url, $this->urls)) {
            return false;
        }
        if ($this->urls[$url]['expire'] < time()) {
            return false;
        }
        return true;
    }

    /**
     * If queue contains URLs, process it !
     * All the que may not be processed, it depend on $this->timeout,
     * Which is set during object creation.
     *
     * @return int                          Number of new URL analysed (iundependent from status)
     *
     * @throws Missingextensionexception    If curl is not installed
     * @throws RuntimeException             If curl failed
     */
    public function processqueue(): int
    {
        if (!extension_loaded('curl')) {
            throw new Missingextensionexception("PHP Curl extension is not installed");
        }

        if (empty($this->queue)) {
            return 0;
        }

        $this->queue = array_unique($this->queue);

        $multihandle = curl_multi_init();
        curl_multi_setopt($multihandle, CURLMOPT_MAX_TOTAL_CONNECTIONS, 10);
        // domains that have already been visited once, to avoid rate limit
        $visiteddomains = [];
        $curlhandles = [];

        foreach ($this->queue as $url) {
            $domain = parse_url($url, PHP_URL_HOST);
            if (key_exists($domain, $visiteddomains)) {
                continue;
            }

            $curlhandles[$url] = curl_init($url);
            curl_setopt($curlhandles[$url], CURLOPT_NOBODY, true);
            curl_setopt($curlhandles[$url], CURLOPT_HEADER, true);
            curl_setopt($curlhandles[$url], CURLOPT_RETURNTRANSFER, true);
            // Forcing HTTPGET may give a little more accurate result as it use GET instead of HEAD method.
            // But it cause downloading a lot of data and crash on big files. See issue #505
            // curl_setopt($curlhandles[$url], CURLOPT_HTTPGET, true);
            curl_setopt($curlhandles[$url], CURLOPT_TIMEOUT, $this->timeout);
            curl_setopt($curlhandles[$url], CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curlhandles[$url], CURLOPT_MAXREDIRS, self::MAX_BOUNCE);

            curl_multi_add_handle($multihandle, $curlhandles[$url]);

            $visiteddomains[$domain] = true;
        }


        do {
            $status = curl_multi_exec($multihandle, $unfinishedHandles);
            if ($status !== CURLM_OK) {
                throw new RuntimeException(curl_multi_strerror(curl_multi_errno($multihandle)));
            }

            while (($info = curl_multi_info_read($multihandle)) !== false) {
                if ($info['msg'] === CURLMSG_DONE) {
                    $handle = $info['handle'];
                    curl_multi_remove_handle($multihandle, $handle);
                }
            }

            if ($unfinishedHandles) {
                if ((curl_multi_select($multihandle)) === -1) {
                    throw new RuntimeException(curl_multi_strerror(curl_multi_errno($multihandle)));
                }
            }
        } while ($unfinishedHandles);

        $newurls = [];

        foreach ($curlhandles as $url => $curlhandle) {
            $curlerror = curl_errno($curlhandle);

            if ($curlerror === CURLE_OPERATION_TIMEDOUT && count($this->queue) > 10) {
                // if queue was big, there is chances that timeout is due to curl saturation
                // consider the link as unchecked
                continue;
            }

            if ($curlerror !== CURLE_OK) {
                $response = $curlerror;
            } else {
                $response = curl_getinfo($curlhandle, CURLINFO_HTTP_CODE);
            }

            if ($this->responseisaccepted($response) || $response === 404) {
                $expire = time() + 80 * 24 * 3600 + rand(0, 40 * 24 * 3600); // 100 +-20 days
            } elseif (key_exists($url, $this->urls) && !$this->responseisaccepted($this->urls[$url]['response'])) {
                // If it was already an error before: expire in twice the time since previous timestamp
                $expire = time() + (time() - $this->urls[$url]['timestamp']) * 2;
            } elseif ($response === 429) { // Too many request: let's expire in one hour to avoid another one
                $expire = time() + 3600;
            } elseif ($response > 200) { // default to ten minutes for other error codes
                $expire = time() + 600;
            } else { // for curl error : expire in one minute
                $expire = time() + 60;
            }

            $newurls[$url] = [
                'response' => $response,
                'timestamp' => time(),
                'expire' => $expire,
            ];
        }

        curl_multi_close($multihandle);

        $this->urls = array_merge($this->urls, $newurls);

        return count($newurls);
    }

    /**
     * @param int $response                 HTTP response code
     *
     * @return bool                         Indicate if code mean alive or not.
     */
    public static function responseisaccepted(int $response): bool
    {
        return key_exists($response, self::ACCEPTED_RESPONSE_CODES);
    }

    /**
     * Save the cache
     *
     * @throws Filesystemexception          if the process failed
     */
    public function savecache(): void
    {
        Fs::writefile(Model::URLS_FILE, json_encode($this->urls, JSON_PRETTY_PRINT));
    }
}
