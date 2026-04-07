<?php

namespace Wcms;

/**
 * A comment sent by a logged in user
 */
class Commentuser extends Comment
{
    protected string $user = '';

    public const TYPE = 'user';

    public function validate(Commentconf $conf): bool
    {
        if (parent::validate($conf) === false) {
            return false;
        }
        if (empty($this->user)) {
            return false;
        }
        return true;
    }

    public function visiblename(): string
    {
        return $this->user;
    }

    public function user(): string
    {
        return $this->user;
    }

    public function setuser(string $user): void
    {
        $this->user = $user;
    }
}
