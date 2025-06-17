<?php

namespace Wcms;

use DateTimeInterface;
use DomainException;
use IntlDateFormatter;

class Clock extends Item
{
    protected string $fullmatch;

    protected string $options;

    protected DateTimeInterface $datetime;

    protected string $type;

    protected string $format = self::SHORT;

    protected string $lang;

    public const DATE = 'DATE';
    public const TIME = 'TIME';
    public const DATEMODIF = 'DATEMODIF';
    public const TIMEMODIF = 'TIMEMODIF';

    public const TYPES = [
        self::DATE => 'date',
        self::TIME => 'date',
        self::DATEMODIF => 'datemodif',
        self::TIMEMODIF => 'datemodif',
    ];

    public const NONE   = 'none';
    public const SHORT  = 'short';
    public const MEDIUM = 'medium';
    public const LONG   = 'long';
    public const FULL   = 'full';

    public const FORMATS = [
        self::NONE      => IntlDateFormatter::NONE,
        self::SHORT     => IntlDateFormatter::SHORT,
        self::MEDIUM    => IntlDateFormatter::MEDIUM,
        self::LONG      => IntlDateFormatter::LONG,
        self::FULL      => IntlDateFormatter::FULL,
    ];

    /**
     * Initiate Date object.
     * Set default locale to Config lang parameter.
     * Specific locale must be set later using setlang() method.
     *
     * @param string $type                  Define if it must display time or date.
     * @param Page $page                    The page which is rendered
     * @param string $fullmatch
     * @param string $options
     */
    public function __construct(
        string $type,
        Page $page,
        string $fullmatch,
        string $options,
        string $lang = null
    ) {
        $this->settype($type);
        $datetype = self::TYPES[$this->type];
        $this->datetime = $page->$datetype();
        $this->fullmatch = $fullmatch;
        $this->options = $options;
        $this->lang = empty($lang) ? Config::lang() : $lang;

        $this->readoptions();
    }

    protected function readoptions(): void
    {
        parse_str(htmlspecialchars_decode($this->options), $datas);
        $datas = array_diff_key($datas, ['type' => 0]); // To avoid erasing type
        $this->hydrate($datas);
    }

    protected function visible(): string
    {
        $formater = new IntlDateFormatter(
            $this->lang,
            $this->isdate() ? self::FORMATS[$this->format] : self::FORMATS[self::NONE],
            $this->istime() ? self::FORMATS[$this->format] : self::FORMATS[self::NONE]
        );
        return $formater->format($this->datetime);
    }

    protected function title(): string
    {
        $formater = new IntlDateFormatter(
            $this->lang,
            $this->isdate() ? self::FORMATS[self::FULL] : self::FORMATS[self::NONE],
            $this->istime() ? self::FORMATS[self::LONG] : self::FORMATS[self::NONE]
        );
        return $formater->format($this->datetime);
    }

    protected function attribute(): string
    {
        if ($this->isdate()) {
            return $this->datetime->format('Y-m-d');
        }
        if ($this->istime()) {
            return $this->datetime->format('H:i');
        }
        return '';
    }

    /**
     * Produce HTML time tag ready to be included.
     */
    public function format(): string
    {
        $attribute = $this->attribute();
        $title = $this->title();
        $visible = $this->visible();
        $type = self::TYPES[$this->type];
        return "<time datetime=\"$attribute\" title=\"$title\" class=\"$type\">$visible</time>";
    }



    // ______________________ G E T _____________________________

    public function fullmatch(): string
    {
        return $this->fullmatch;
    }

    public function options(): string
    {
        return $this->options;
    }

    // _______________________ S E T ______________________________

    public function settype(string $type): void
    {
        if (key_exists($type, self::TYPES)) {
            $this->type = $type;
        } else {
            $allowedtypes = implode(' or ', array_keys(self::TYPES));
            throw new DomainException("$type type is not valid. Should be either $allowedtypes");
        }
    }

    /**
     * Formater must be a key in FORMATS constant
     */
    public function setformat(string $format): void
    {
        if (key_exists($format, self::FORMATS)) {
            $this->format = $format;
        }
    }

    public function setlang(string $lang): void
    {
        if (strlen($lang) < Config::LANG_MAX && strlen($lang) >= Config::LANG_MIN) {
            $this->lang = $lang;
        }
    }

    private function isdate(): bool
    {
        return (str_starts_with($this->type, 'DATE'));
    }

    private function istime(): bool
    {
        return (str_starts_with($this->type, 'TIME'));
    }
}
