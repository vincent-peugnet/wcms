<?php

class Event extends Dbitem
{
    protected $id;
    protected $date;
    protected $type;
    protected $user;
    protected $target;
    protected $message;
    protected $clap = 0;

    const EVENT_TYPES = ['message', 'art_add', 'art_edit', 'art_delete', 'media_add', 'media_delete', 'font_add'];
    const EVENT_BASE = ['message'];
    const EVENT_ART = ['art_add', 'art_edit', 'art_delete'];
    const EVENT_MEDIA = ['media_add', 'media_delete'];
    const EVENT_FONT = ['font_add', 'font_delete'];
    const MESSAGE_MAX_LENGTH = 2 ** 10;

    const VAR_DATE = ['date'];

    public function __construct($datas)
    {
        $this->hydrate($datas);
    }

    public function stamp()
    {
        $this->date = new DateTimeImmutable(null, timezone_open("Europe/Paris"));
        $this->user = idclean($this->user);
        if (in_array($this->type, self::EVENT_ART)) {
            $this->target = idclean($this->target);
        } elseif ($this->type === 'message') {
            $this->message = htmlspecialchars($this->message);
        }
    }

    public function addclap()
    {
        $this->clap ++;
    }

    // _____________________ G E T __________________________

    public function id()
    {
        return $this->id;
    }

    public function date($type = 'datetime')
    {
        switch ($type) {
            case 'datetime':
                return $this->date;
                break;

            case 'string':
                return $this->date->format(DateTime::ISO8601);
                break;

            case 'hrdi':
                $now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));
                return hrdi($this->date->diff($now));
                break;

        }
    }

    public function type()
    {
        return $this->type;
    }

    public function user()
    {
        return $this->user;
    }

    public function target()
    {
        return $this->target;
    }

    public function message()
    {
        return $this->message;
    }

    public function clap()
    {
        return $this->clap;
    }



    // ________________________ S E T ____________________

    public function setid($id)
    {
        if (is_int($id)) {
            $this->id = $id;
        }
    }

    public function setdate($date)
    {
        if ($date instanceof DateTimeImmutable) {
            $this->date = $date;
        } elseif (is_string($date)) {
            $this->date = DateTimeImmutable::createFromFormat(DateTime::ISO8601, $date, new DateTimeZone('Europe/Paris'));
        }
    }

    public function settype($type)
    {
        if (in_array($type, self::EVENT_TYPES)) {
            $this->type = $type;
        }
    }

    public function setuser($user)
    {
        if (is_string($user) && strlen($user) < Model::MAX_ID_LENGTH) {
            $this->user = $user;
        }
    }

    public function settarget($target)
    {
        if (is_string($target) && strlen($target) < Model::MAX_ID_LENGTH) {
            $this->target = $target;
        }
    }

    public function setmessage($message)
    {
        if (is_string($message) && strlen($message) < self::MESSAGE_MAX_LENGTH) {
            $this->message = $message;
        }
    }

    public function setclap($clap)
    {
        if(is_int($clap)) {
            $this->clap = $clap;
        }
    }





}


?>