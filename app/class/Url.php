<?php

namespace Wcms;

use DateTimeImmutable;

class Url extends Item
{
    public string $id;
    public int $response = 0;
    public int $timestamp = 0;
    public int $expire = 0;
    public string $message = '';
    public bool $accepted;

    /**
     * @var array<string, null> list of pages that include this url `id => null`
     */
    public array $pages = [];

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(string $url, array $data)
    {
        $this->id = $url;
        $this->hydrate($data);
        $this->accepted = Serviceurlchecker::responseisaccepted($this->response);
    }

    public function timestampdate(): DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat('U', strval($this->timestamp));
    }

    public function expiredate(): DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat('U', strval($this->expire));
    }

    public function addpage(string $page): void
    {
        $this->pages[$page] = null;
    }

    public function removepage(string $page): void
    {
        unset($this->pages[$page]);
    }

    public function setresponse(int $response): void
    {
        $this->response = $response;
    }

    public function settimestamp(int $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function setexpire(int $expire): void
    {
        $this->expire = $expire;
    }

    public function setmessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @param array<string, null> $pages
     */
    public function setpages(array $pages): void
    {
        $this->pages = $pages;
    }
}
