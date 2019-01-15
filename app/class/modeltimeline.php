<?php

class Modeltimeline extends Modeldb
{
	const EVENT_BASE = ['message'];
	const EVENT_ART = ['art_add', 'art_edit', 'art_delete'];
	const EVENT_MEDIA = ['media_add', 'media_delete'];
	const EVENT_FONT = ['font_add', 'font_delete'];

	public function __construct()
	{
		parent::__construct();
		$this->storeinit('timeline');
	}

	public function get(int $id)
	{
		$eventdata = $this->repo->findById($id);
		if ($eventdata !== false) {
			return new Event($eventdata);
		} else {
			return false;
		}
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
			$id = intval($event->id());
			$eventlist[$id] = $event;
		}
		return $eventlist;
	}


	public function getlisterid(array $idlist = []) : array
	{
		$eventdatalist = $this->repo->query()
			->where('__id', 'IN', $idlist)
			->execute();

		$eventlist = [];
		foreach ($eventdatalist as $id => $eventdata) {
			$eventlist[$id] = new Event($eventdata);
		}
		return $eventlist;
	}


	/**
	 * Store event
	 * 
	 * @param Event The event to be stored in the repositery
	 * 
	 * @return bool retrun true if it works, false if it fails
	 */
	public function add(Event $event) : bool
	{
		$eventdata = new \JamesMoss\Flywheel\Document($event->dry());
		$eventdata->setId($event->id());
		$result = $this->repo->store($eventdata);
		return $result;
	}

	/**
	 * Return last free id
	 * 
	 * @return int id
	 */
	public function getlastfreeid() : int
	{
		$idlist = $this->list();

		if (!empty($idlist)) {
			$id = max($idlist);
			$id++;
		} else {
			$id = 1;
		}
		return $id;
	}

	public function group(array $events)
	{
		$id = 0;
		$subid = 0;
		$lastuser = null;
		foreach ($events as $event) {
			if($event->user() !== $lastuser) {
				$subid = 0;
				$id ++;
				$groupedevents[$id]['user'] = $event->user();
			} else {
				$subid ++;
			}
			$groupedevents[$id][$subid] = $event;
			$lastuser = $event->user();
		}
		return $groupedevents;
	}

	public function showlast(array $types, int $qty = 25, int $offset = 0)
	{
		$types = array_intersect($types, $this->types());

		$eventdatalist = $this->repo->query()
			->where('type', 'IN', $types)
			->orderBy('date DESC')
			->limit($qty, $offset)
			->execute();

		$eventlist = [];
		foreach ($eventdatalist as $id => $eventdata) {
			$eventlist[] = new Event($eventdata);
		}

		$eventlist = array_reverse($eventlist);
		return $eventlist;
	}


	public function types()
	{
		return array_merge(self::EVENT_ART, self::EVENT_BASE, self::EVENT_MEDIA, self::EVENT_MEDIA);
	}


}


?>