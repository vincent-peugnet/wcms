<?php

class Modeltimeline extends Modeldb
{

	public function __construct()
	{
		parent::__construct();
		$this->storeinit('timeline');
    }
    
    /**
     * Retrun a list of Event objects
     * 
     * @return array array of Event where the key is the Event id.
     */
    public function getlister() : array
	{
		$eventlist = [];
		$datalist = $this->repo->findAll();
		foreach ($datalist as $eventdata) {
			$event = new Event($eventdata);
			$eventlist[$event->id()] = $event;
		}
		return $eventlist;
	}

	public function add(Event $event)
	{
		$eventdata = new \JamesMoss\Flywheel\Document($event->dry());
		$eventdata->setId($event->id());
		$result = $this->repo->store($eventdata);
		return $result;
	}

	public function getlastfreeid()
	{
		$idlist = $this->list();

		if(!empty($idlist)) {
			$id = max($idlist);
			$id ++;
		} else {
			$id = 1;
		}

		return $id;
	}


}


?>