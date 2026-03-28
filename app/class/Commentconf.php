<?php

namespace Wcms;

use RuntimeException;

class Commentconf extends Item
{
    protected string $id;
    protected int $maxlength = Comment::MAX_MESSAGE_LENGTH;
    protected int $minlength = 0;
    protected int $mode = 1; // Not used yet
    protected ?int $limit = null;

    /**
     * @param array<string, mixed> $data
     *
     * @throws RuntimeException
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

    public function mode(): int
    {
        return $this->mode;
    }

    public function limit(): ?int
    {
        return $this->limit;
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

    public function setmode(int $mode): void
    {
        $this->mode = $mode;
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
}
