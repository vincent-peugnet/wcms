<?php

namespace Wcms;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use DateTimeInterface;

class Comment extends Item
{
    protected DateTimeImmutable $date;
    protected string $username;
    protected string $message = '';

    public const MAX_COMMENT_LENGTH = 2 ** 14;

    // public function __construct(string $username, string $message)
    // {
    //     $this->date = new DateTimeImmutable();
    //     $this->username = $username;
    //     $this->message = $message;
    // }

    /**
     * @param array<mixed> $data
     */
    public function __construct(array $data)
    {
        $this->hydrate($data);
    }


    // GET

    public function username(): string
    {
        return $this->username;
    }

    public function message(): string
    {
        return $this->message;
    }

    /**
     * @return DateTimeInterface|string
     */
    public function date(string $option = 'date')
    {
        return $this->datetransform('date', $option);
    }


    // SET

    public function setusername(string $username): void
    {
        $this->username = $username;
    }

    public function setmessage(string $message): void
    {
        if (strlen($message) <= self::MAX_COMMENT_LENGTH) {
            $this->message = $message;
        }
    }

    /**
     * @param DateTimeImmutable|string $date
     */
    public function setdate($date): void
    {
        if ($date instanceof DateTimeImmutable) {
            $this->date = $date;
        } elseif (is_string($date)) {
            $this->date = DateTimeImmutable::createFromFormat(
                DateTime::RFC3339,
                $date,
                new DateTimeZone('Europe/Paris')
            );
        }
    }
}
