<?php

namespace Wcms;

class Pagev2 extends Page
{
    protected int $version = self::V2;

    protected $content;

    public const TABS = ['content', 'css', 'body', 'javascript'];

    public function reset()
    {
        parent::reset();

        $this->setcontent('');

        $this->setinterface('content');
    }

    public function content($type = ''): string
    {
        return $this->content;
    }

    public function primary($type = ''): string
    {
        return $this->content;
    }

    public function setcontent($content)
    {
        if (is_string($content)) {
            $this->content = $content;
        }
    }
}
