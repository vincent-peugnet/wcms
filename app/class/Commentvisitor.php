<?php

namespace Wcms;

use VStelmakh\UrlHighlight\UrlHighlight;

/**
 * A comment sent by a visitor
 */
class Commentvisitor extends Comment
{
    protected string $pseudonym = '';
    protected string $website = '';

    public const TYPE = 'visitor';

    public function validate(Commentconf $conf): bool
    {
        if (parent::validate($conf) === false) {
            return false;
        }

        if ($conf->requirepseudonym() && empty($this->pseudonym)) {
            return false;
        }
        if (!$conf->allowwebsite() && !empty($this->website)) {
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
        return true;
    }

    public function visiblename(): string
    {
        return $this->pseudonym;
    }

    public function pseudonym(): string
    {
        return $this->pseudonym;
    }

    public function website(): string
    {
        return $this->website;
    }

    public function setpseudonym(string $pseudonym): void
    {
        $pseudonym = trim(strip_tags($pseudonym));
        if (strlen($pseudonym) <= self::MAX_PSEUDONYM_LENGTH) {
            $this->pseudonym = $pseudonym;
        }
    }

    public function setwebsite(string $website): void
    {
        $this->website = $website;
    }
}
