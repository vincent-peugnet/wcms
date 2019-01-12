<?php

class Controllertimeline extends Controller
{
    /**
     * @var Modeltimeline
     */
    protected $eventmanager;

    public function __construct($render) {
        parent::__construct($render);
        $this->eventmanager = new Modeltimeline;
    }

    public function desktop()
    {
        $eventlist = $this->eventmanager->getlister();

        $this->showtemplate('timeline', ['eventlist' => $eventlist]);

    }

    public function add()
    {
        $event = new Event($_POST);
        $event->stamp();
        $event->setid($this->eventmanager->getlastfreeid());
        $this->eventmanager->add($event);
        $this->routedirect('timeline');
    }
}








?>