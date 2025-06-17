<?php

namespace Wcms;

class Pagev2 extends Page
{
    protected int $version = self::V2;

    protected string $content;

    public const TABS = ['content', 'css', 'body', 'javascript'];

    public function reset(): void
    {
        parent::reset();

        $this->setcontent('');

        $this->setinterface('content');
    }

    public function content(string $type = ''): string
    {
        return $this->content;
    }

    public function primary(string $type = ''): string
    {
        return $this->content;
    }

    public function setcontent(string $content): void
    {
        $this->content = $content;
    }
}
