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

    /** @var int CACHE_EXPIRE_TIME in days */
    public const CACHE_EXPIRE_TIME = 90;

    /** @var null[] URL response code considered as not dead */
    public const ACCEPTED_RESPONSE_CODES = [
        200 => null,
        401 => null,
        403 => null,
    ];

    /**
     * Tool that check for urls status, first in the cache, then on the Web
     * The cache expires according to CACHE_EXPIRE_TIME constant
     * A timeout have to be set to limit Web checking time
     *
     * @param int $timeout             allocated time for looking URL on the Web (in seconds)
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
     *
     * @param string $url                   The URL to verify
     *
     * @return bool                         True if the url is alive, false if it's dead
     *
     * @throws RuntimeException             If the status of the URL is not cached
     */
    public function check(string $url): bool
    {
        // $url = filter_var($url, FILTER_SANITIZE_URL);
        if ($this->iscachedandvalid($url)) {
            return key_exists($this->urls[$url]['response'], self::ACCEPTED_RESPONSE_CODES);
        } else {
            $this->queue[] = $url;
            throw new RuntimeException('unchecked URL');
        }
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
        if (($this->urls[$url]['timestamp'] + self::CACHE_EXPIRE_TIME * 3600 * 24) < time()) {
            unset($this->urls[$url]);
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

        foreach ($this->queue as $url) {
            $curlhandles[$url] = curl_init($url);
            curl_setopt($curlhandles[$url], CURLOPT_NOBODY, true);
            curl_setopt($curlhandles[$url], CURLOPT_HEADER, true);
            curl_setopt($curlhandles[$url], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curlhandles[$url], CURLOPT_HTTPGET, true);
            curl_setopt($curlhandles[$url], CURLOPT_TIMEOUT, $this->timeout);
            curl_setopt($curlhandles[$url], CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curlhandles[$url], CURLOPT_MAXREDIRS, self::MAX_BOUNCE);

            curl_multi_add_handle($multihandle, $curlhandles[$url]);
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
            switch (curl_errno($curlhandle)) {
                case CURLE_OK:
                    $newurls[$url] = [
                        'response' => curl_getinfo($curlhandle, CURLINFO_HTTP_CODE),
                        'timestamp' => time()
                    ];
                    break;
                case CURLE_OPERATION_TIMEDOUT:
                    if (count($this->queue) < 5 && $this->timeout >= 3) {
                        $newurls[$url] = [
                            'response' => 0,
                            'timestamp' => time() - ( self::CACHE_EXPIRE_TIME - 1 ) * 24 * 3600,
                        ];
                    }
                    break;
                case CURLE_COULDNT_RESOLVE_HOST:
                default:
                    $newurls[$url] = [
                        'response' => 0,
                        'timestamp' => time(),
                    ];
            }
        }

        curl_multi_close($multihandle);

        $this->urls = array_merge($this->urls, $newurls);

        return count($newurls);
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
