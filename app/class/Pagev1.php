<?php

namespace Wcms;

class Pagev1 extends Page
{
    protected int $version = self::V1;

    protected $header;
    protected $main;
    protected $nav;
    protected $aside;
    protected $footer;

    public const TABS = ['main', 'css', 'header', 'nav', 'aside', 'footer','body', 'javascript'];

    public function reset()
    {
        parent::reset();

        $this->setheader('');
        $this->setmain('');
        $this->setnav('');
        $this->setaside('');
        $this->setfooter('');

        $this->setinterface('main');
    }


    public function header($type = 'string')
    {
        return $this->header;
    }

    public function main($type = 'string')
    {
        return $this->main;
    }

    public function primary($type = ''): string
    {
        return $this->main;
    }

    public function nav($type = "string")
    {
        return $this->nav;
    }

    public function aside($type = "string")
    {
        return $this->aside;
    }

    public function footer($type = "string")
    {
        return $this->footer;
    }


    public function setheader($header)
    {
        if (strlen($header) < self::LENGTH_LONG_TEXT && is_string($header)) {
            $header = crlf2lf($header);
            $this->header = $header;
        }
    }

    public function setmain($main)
    {
        if (strlen($main) < self::LENGTH_LONG_TEXT and is_string($main)) {
            $main = crlf2lf($main);
            $this->main = $main;
        }
    }

    public function setnav($nav)
    {
        if (strlen($nav) < self::LENGTH_LONG_TEXT and is_string($nav)) {
            $nav = crlf2lf($nav);
            $this->nav = $nav;
        }
    }

    public function setaside($aside)
    {
        if (strlen($aside) < self::LENGTH_LONG_TEXT and is_string($aside)) {
            $aside = crlf2lf($aside);
            $this->aside = $aside;
        }
    }

    public function setfooter($footer)
    {
        if (strlen($footer) < self::LENGTH_LONG_TEXT and is_string($footer)) {
            $footer = crlf2lf($footer);
            $this->footer = $footer;
        }
    }
}
