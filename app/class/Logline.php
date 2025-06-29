<?php

namespace Wcms;

use DateTime;
use DateTimeInterface;
use RuntimeException;

class Logline
{
    public DateTime $date;
    public string $level;
    public string $ref;
    public string $message;
    public string $in = '';

    public const LEVELS = ['ERROR', 'WARN', 'INFO', 'DEBUG'];

    /**
     * @throws RuntimeException if parsing failed
     */
    public function __construct(string $line)
    {
        $length = strlen($line);
        $date = substr($line, 0, 25);
        $datetime = DateTime::createFromFormat(DateTimeInterface::ATOM, $date);

        if ($datetime === false) {
            throw new RuntimeException("Date $date is not parsable from '$line'");
        }
        $this->date = $datetime;

        $level = substr($line, 28, 5);
        $level = trim($level);
        if (in_array($level, self::LEVELS)) {
            $this->level = strtolower($level);
        }

        $rest = substr($line, 36);

        $refpos = strpos($rest, ' ');

        if ($refpos === false) {
            throw new RuntimeException();
        }
        $this->ref = substr($rest, 0, $refpos);

        $rest = substr($rest, $refpos + 1);

        $inpos = strrpos($rest, ' in /');

        if ($inpos === false) {
            $this->message = $rest;
        } else {
            $this->message = substr($rest, 0, $inpos);
            $this->in = substr($rest, $inpos + 4);
        }
    }
}
