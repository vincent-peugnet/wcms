<?php

namespace Wcms;

use Wcms\Exception\Filesystemexception;

/**
 * Check URL
 */
class Serviceurlchecker
{
    /** @var array[] $urls */
    protected array $urls = [];

    /** @var int MAX_BOUNCE limit of redirections to follow */
    public const MAX_BOUNCE = 8;

    /** @var int CACHE_EXPIRE_TIME in days */
    public const CACHE_EXPIRE_TIME = 30;

    public function __construct()
    {
        try {
            $urlfile = Fs::readfile(Model::URLS_FILE);
            $this->urls = json_decode($urlfile, true);
        } catch (Filesystemexception $e) {
            // This mean the tag file does not exist
        }
    }

    /**
     * Check if URL response is 200
     */
    public function is200(string $url): bool
    {
        if (!$this->iscached($url)) {
            $this->urls[$url]['response'] = $this->getresponse($url);
            $this->urls[$url]['timestamp'] = time();
        }
        return $this->urls[$url]['response'] === 200;
    }

    /**
     * read HTTP response headers
     *
     * @return int                          HTTP response code, or `0` if no response
     */
    protected function getresponse(string $url): int
    {
        $headers = @get_headers($url, 1); // `@` avoid throwing PHP error
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

    protected function iscached(string $url): bool
    {
        if (!key_exists($url, $this->urls)) {
            return false;
        }
        return !($this->urls[$url]['timestamp'] < (time() - self::CACHE_EXPIRE_TIME * 3600 * 24));
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
