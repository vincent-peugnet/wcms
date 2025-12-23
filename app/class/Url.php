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

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(string $url, array $data)
    {
        $this->id = $url;
        $this->hydrate($data);
    }

    public function timestampdate(): DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat('U', strval($this->timestamp));
    }

    public function expiredate(): DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat('U', strval($this->expire));
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
}
