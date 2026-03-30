<?php

namespace Wcms;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use DateTimeInterface;
use VStelmakh\UrlHighlight\UrlHighlight;

class Comment extends Item
{
    protected DateTimeImmutable $date;
    protected string $username = '';
    protected string $pseudonym = '';
    protected string $website = '';
    protected string $message = '';

    public const MAX_MESSAGE_LENGTH = 2 ** 14;

    public const MAX_PSEUDONYM_LENGTH = 128;

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

    public function validate(Commentconf $conf): bool
    {
        if (strlen($this->message) > $conf->maxlength()) {
            return false;
        }
        if (strlen($this->message) < $conf->minlength()) {
            return false;
        }

        // depending on the comment mode, only the pseudonym or username property could be filled
        switch ($conf->mode()) {
            case Commentconf::VISITOR_MODE:
                if (!empty($this->username)) {
                    return false;
                }
                if ($conf->requirepseudonym() && empty($this->pseudonym)) {
                    return false;
                }
                if ($conf->requirewebsite() && empty($this->website)) {
                    return false;
                }
                if (!empty($this->website)) {
                    $urlHighlight = new UrlHighlight();
                    if (!$urlHighlight->isUrl($this->website)) {
                        return false;
                    }
                }
                break;

            case Commentconf::USER_MODE:
                if (empty($this->username) || !empty($this->pseudonym) || !empty($this->website)) {
                    return false;
                }
                break;
        }

        return true;
    }


    // GET

    public function username(): string
    {
        return $this->username;
    }

    public function pseudonym(): string
    {
        return $this->pseudonym;
    }

    public function website(): string
    {
        return $this->website;
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

    public function setpseudonym(string $pseudonym): void
    {
        $pseudonym = trim(strip_tags($pseudonym));
        if (strlen($pseudonym) <= self::MAX_PSEUDONYM_LENGTH) {
            $this->pseudonym = $pseudonym;
        }
    }

    public function setmessage(string $message): void
    {
        if (strlen($message) <= self::MAX_MESSAGE_LENGTH) {
            $this->message = $message;
        }
    }

    public function setwebsite(string $website): void
    {
        $this->website = $website;
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
