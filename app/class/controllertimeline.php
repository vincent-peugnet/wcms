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
        $eventlist = $this->eventmanager->showlast(['message'], 100, 0);

        $groupedeventlist = $this->eventmanager->group($eventlist);

        $this->showtemplate('timeline', ['eventlist' => $eventlist, 'groupedeventlist' => $groupedeventlist]);

    }

    public function add()
    {
        if($this->user->level() >= Modeluser::EDITOR && !empty($_POST['message'])) {

            $event = new Event($_POST);
            $event->stamp();
            $event->setid($this->eventmanager->getlastfreeid());
            $this->eventmanager->add($event);
        }
        $this->routedirect('timeline');
    }

    public function clap()
    {
        if(isset($_POST['id']) && isset($_POST['clap'])) {
            $event = $this->eventmanager->get(intval($_POST['id']));
            $event->addclap();
            $this->eventmanager->add($event);
        }
        $this->routedirect('timeline');

    }
}








?>