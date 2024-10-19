<?php

namespace Wcms;

use RuntimeException;
use Wcms\Exception\Filesystemexception;

/**
 * Check URL
 */
class Serviceurlchecker
{
    /** @var array[] $urls */
    protected array $urls = [];

    /** @var int $starttimestamp timestamp lauched when object is build (in seconds) */
    protected int $starttimestamp;

    /** @var int $webchecktime time before stopping Web check (in seconds) */
    protected int $webchecktime;

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
     * A time limite have to be set to limit Web checking time
     *
     * @param int $webchecktime             allocated time for looking URL on the Web (in seconds)
     *                                      if set to `0`, Check on the Web is disabled: only the cache is used
     */
    public function __construct(int $webchecktime)
    {
        $this->webchecktime = $webchecktime;
        if ($webchecktime === 0) {
            $this->cacheonly = true;
        }
        $this->starttimestamp = time();
        try {
            $urlfile = Fs::readfile(Model::URLS_FILE);
            $this->urls = json_decode($urlfile, true);
        } catch (Filesystemexception $e) {
            // This mean the tag file does not exist
        }
    }

    /**
     * Check if URL is dead according to ACCEPTED_RESPONSE_CODES
     *
     * @throws RuntimeException             If time limit is reached and URL status is expired or not stored in cache
     */
    public function isdead(string $url): bool
    {
        if ($this->iscachedandvalid($url)) {
            return !key_exists($this->urls[$url]['response'], self::ACCEPTED_RESPONSE_CODES);
        }
        if (!$this->cacheonly && time() < ($this->starttimestamp + $this->webchecktime)) {
            $this->urls[$url]['response'] = $this->getresponse($url);
            $this->urls[$url]['timestamp'] = time();
            return !key_exists($this->urls[$url]['response'], self::ACCEPTED_RESPONSE_CODES);
        }
        throw new RuntimeException('Impossible to give a status about this URL');
    }

    /**
     * read HTTP response headers
     *
     * @return int                          HTTP response code, or `0` if no response
     */
    protected function getresponse(string $url): int
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        $context = stream_context_create([$scheme => ['method' => "HEAD",'header' => 'User-Agent: Mozilla/5.0']]);
        $headers = @get_headers($url, 1, $context); // `@` avoid throwing PHP error
        if ($headers === false) {
            return 0;
        }
        for ($i = 0; $i < self::MAX_BOUNCE; $i++) {
            if (!isset($headers[$i])) {
                $id = $i - 1;
                $http = $headers[$id];
                return intval(substr($http, 9, 3));
            }
        }
        return 0;
    }

    /**
     * Check if the status of URL is cached and has not expired
     * If cache is expired, the entry is deleted
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
     * Save the cache
     *
     * @throws Filesystemexception          if the process failed
     */
    public function savecache(): void
    {
        Fs::writefile(Model::URLS_FILE, json_encode($this->urls, JSON_PRETTY_PRINT));
    }
}
