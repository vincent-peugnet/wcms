<?php

namespace Wcms;

class Pagev1 extends Page
{
    protected int $version = self::V1;

    protected string $header;
    protected string $main;
    protected string $nav;
    protected string $aside;
    protected string $footer;

    public const TABS = ['main', 'css', 'header', 'nav', 'aside', 'footer','body', 'javascript'];
    public const HTML_ELEMENTS = ['header', 'nav', 'main', 'aside', 'footer'];

    public function reset(): void
    {
        parent::reset();

        $this->setheader('');
        $this->setmain('');
        $this->setnav('');
        $this->setaside('');
        $this->setfooter('');

        $this->setinterface('main');
    }


    public function header(string $type = 'string'): string
    {
        return $this->header;
    }

    public function main(string $type = 'string'): string
    {
        return $this->main;
    }

    public function primary(string $type = ''): string
    {
        return $this->main;
    }

    public function nav(string $type = "string"): string
    {
        return $this->nav;
    }

    public function aside(string $type = "string"): string
    {
        return $this->aside;
    }

    public function footer(string $type = "string"): string
    {
        return $this->footer;
    }


    public function setheader(string $header): void
    {
        if (strlen($header) < self::LENGTH_LONG_TEXT) {
            $header = crlf2lf($header);
            $this->header = $header;
        }
    }

    public function setmain(string $main): void
    {
        if (strlen($main) < self::LENGTH_LONG_TEXT) {
            $main = crlf2lf($main);
            $this->main = $main;
        }
    }

    public function setnav(string $nav): void
    {
        if (strlen($nav) < self::LENGTH_LONG_TEXT) {
            $nav = crlf2lf($nav);
            $this->nav = $nav;
        }
    }

    public function setaside(string $aside): void
    {
        if (strlen($aside) < self::LENGTH_LONG_TEXT) {
            $aside = crlf2lf($aside);
            $this->aside = $aside;
        }
    }

    public function setfooter(string $footer): void
    {
        if (strlen($footer) < self::LENGTH_LONG_TEXT) {
            $footer = crlf2lf($footer);
            $this->footer = $footer;
        }
    }
}
