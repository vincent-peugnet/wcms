<?php


class Application
{

    protected $config;


    public function __construct()
    {
        $this->setconfig();

    }


    public function setconfig()
    {
        $this->config = Modelconfig::readconfig();
    }




}










?>