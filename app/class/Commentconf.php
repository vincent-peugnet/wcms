<?php

namespace Wcms;

use RuntimeException;

class Commentconf extends Item
{
    protected string $id;
    protected int $maxlength = Comment::MAX_MESSAGE_LENGTH;
    protected int $minlength = 0;
    protected string $mode = self::USER_MODE;
    protected ?int $limit = self::DEFAULT_LIMIT;

    /**
     * @var bool $requirepseudonym define if message field is mandatory.
     */
    protected bool $requiremessage = false;

    /**
     * @var bool $allowpseudonym define if pseudonym field can be set.
     * This only have an effect in VISITOR_MODE
     */
    protected bool $allowpseudonym = false;

    /**
     * @var bool $requirepseudonym define if pseudonym field is mandatory.
     * This only have an effect in VISITOR_MODE
     */
    protected bool $requirepseudonym = false;

    /**
     * @var bool $allowwebsite define if website field can be set.
     * This only have an effect in VISITOR_MODE
     */
    protected bool $allowwebsite = false;

    /**
     * @var bool $requirewebsite define if website field is mandatory.
     * This only have an effect in VISITOR_MODE
     */
    protected bool $requirewebsite = false;

    /**
     * @var string $success message that should be displayed to page viewer in case of success
     */
    protected string $success = '';

    public const VISITOR_MODE = 'visitor';
    public const USER_MODE = 'user';
    public const ALL_MODE = 'all';
    public const MODES = [self::VISITOR_MODE, self::USER_MODE, self::ALL_MODE];
    public const DEFAULT_LIMIT = 100;

    /**
     * @param array<string, mixed> $data
     *
     * @throws RuntimeException if ID is empty or invalid
     */
    public function __construct(string $id, array $data = [])
    {
        if (Model::idcheck($id)) {
            $this->id = $id;
        }
        $this->hydrate($data);

        if (!isset($this->id)) {
            throw new RuntimeException('missing or invalid ID');
        }
    }

    /**
     * Indicate if visitor are allowed to post comment
     */
    public function allowvisitor(): bool
    {
        return $this->mode !== self::USER_MODE;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function maxlength(): int
    {
        return $this->maxlength;
    }

    public function minlength(): int
    {
        return $this->minlength;
    }

    public function mode(): string
    {
        return $this->mode;
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    public function requiremessage(): bool
    {
        return $this->requiremessage;
    }

    public function allowpseudonym(): bool
    {
        return $this->allowpseudonym;
    }

    public function requirepseudonym(): bool
    {
        return $this->requirepseudonym;
    }

    public function requirewebsite(): bool
    {
        return $this->requirewebsite;
    }

    public function allowwebsite(): bool
    {
        return $this->allowwebsite;
    }

    public function success(): string
    {
        return $this->success;
    }

    /**
     * @return bool                         indicting if setting was valid or not
     */
    public function setmaxlength(int $maxlength): bool
    {
        if ($maxlength < 0 || $maxlength > Comment::MAX_MESSAGE_LENGTH) {
            return false;
        }

        $this->maxlength = $maxlength;
        return true;
    }

    public function setminlength(int $minlength): void
    {
        if ($minlength > 0) {
            $this->minlength = $minlength;
        }
    }

    public function setmode(string $mode): void
    {
        if (in_array($mode, self::MODES)) {
            $this->mode = $mode;
        }
    }

    /**
     * @param ?int<0, max> $limit           set to NULL to disable limit
     */
    public function setlimit(?int $limit): void
    {
        if ($limit === null) {
            $this->limit = null;
        }
        if ($limit >= 0) {
            $this->limit = $limit;
        }
    }

    public function setrequiremessage(bool $require): void
    {
        $this->requiremessage = $require;
    }

    public function setallowpseudonym(bool $allow): void
    {
        $this->allowpseudonym = $allow;
    }

    public function setrequirepseudonym(bool $require): void
    {
        $this->requirepseudonym = $require;
    }

    public function setrequirewebsite(bool $require): void
    {
        $this->requirewebsite = $require;
    }

    public function setallowwebsite(bool $allow): void
    {
        $this->allowwebsite = $allow;
    }

    public function setsuccess(string $success): void
    {
        $this->success = $success;
    }
}
