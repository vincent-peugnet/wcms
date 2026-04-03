<?php

namespace Wcms;

/**
 * A comment sent by a logged in user
 */
class Commentuser extends Comment
{
    protected string $username = '';

    public function validate(Commentconf $conf): bool
    {
        if (parent::validate($conf) === false) {
            return false;
        }
        if (empty($this->username)) {
            return false;
        }
        return true;
    }

    public function visiblename(): string
    {
        return $this->username;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function setusername(string $username): void
    {
        $this->username = $username;
    }
}
