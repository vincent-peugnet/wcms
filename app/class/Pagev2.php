<?php

namespace Wcms;

class Pagev2 extends Page
{
    protected int $version = self::V2;

    protected $markdown;

    public const TABS = ['markdown', 'css', 'body', 'javascript'];

    public function reset()
    {
        parent::reset();

        $this->setmarkdown('');

        $this->setinterface('markdown');
    }

    public function markdown($type = ''): string
    {
        return $this->markdown;
    }

    public function primary($type = ''): string
    {
        return $this->markdown;
    }

    public function setmarkdown($markdown)
    {
        if (is_string($markdown)) {
            $this->markdown = $markdown;
        }
    }
}
