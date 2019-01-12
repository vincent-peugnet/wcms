<?php

class Modeltimeline extends Modeldb
{

	public function __construct()
	{
		parent::__construct();
		$this->storeinit('timeline');
	}
}


?>