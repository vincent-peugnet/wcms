<?php

class Controllertimeline extends Controller
{
    /**@var Modeltimeline */
    protected $eventmanager;

    public function __construct($render) {
        parent::__construct($render);
        $this->eventmanager = new Modeltimeline;
    }

    public function desktop()
    {
        var_dump($this->eventmanager->list());
    }
}








?>