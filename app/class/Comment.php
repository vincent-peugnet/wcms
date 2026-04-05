<?php

namespace Wcms;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use DateTimeInterface;

abstract class Comment extends Item
{
    protected DateTimeImmutable $date;
    protected string $message = '';

    /**
     * @var bool $approved indicate if the comment has been approved
     */
    protected bool $approved = false;

    public const MAX_MESSAGE_LENGTH = 2 ** 14;

    public const MAX_PSEUDONYM_LENGTH = 128;

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
        return true;
    }

    /**
     * The name that shoule be displayed in internal interface
     * It may be the pseudonym of visitor comment or the username of logged in user comment
     */
    public function visiblename(): string
    {
        return '';
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return Commentuser|Commentvisitor
     */
    public static function new(array $data): Comment
    {
        if (isset($data['user']) && !empty($data['user'])) {
            return new Commentuser($data);
        } else {
            return new Commentvisitor($data);
        }
    }


    // GET


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

    public function approved(): bool
    {
        return $this->approved;
    }


    // SET



    public function setmessage(string $message): void
    {
        if (strlen($message) <= self::MAX_MESSAGE_LENGTH) {
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

    public function setapproved(bool $approved): void
    {
        $this->approved = $approved;
    }
}
