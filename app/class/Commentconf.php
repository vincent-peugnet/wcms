<?php

namespace Wcms;

use RuntimeException;

class Commentconf extends Item
{
    public string $id;
    public int $maxlength = Comment::MAX_COMMENT_LENGTH;
    public int $minlength = 0;
    public int $mode; // Not used yet

    /**
     * @param array<string, mixed> $data
     *
     * @throws RuntimeException
     */
    public function __construct(array $data = [])
    {
        $this->hydrate($data);

        if (!isset($this->id)) {
            throw new RuntimeException('missing ID');
        }
        if (!isset($this->mode)) {
            throw new RuntimeException('missing Mode');
        }
        if ($this->minlength > $this->maxlength) {
            throw new RuntimeException('minlength is superior to maxlenght');
        }
    }

    public function setid(string $id): void
    {
        if (Model::idcheck($id)) {
            $this->id = $id;
        }
    }

    public function setmaxlength(int $maxlength): void
    {
        if ($maxlength > 0) {
            $this->maxlength = $maxlength;
        }
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
}
