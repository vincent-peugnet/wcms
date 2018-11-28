<?php

class Controllerfont extends Controller
{

    protected $fontmanager;
    
    public function __construct($router)
    {
        parent::__construct($router);
        $this->fontmanager = new Modelfont();

    }

    public function desktop()
    {
        var_dump($this->fontmanager->getfontlist());
    }
    
    public function render()
    {
        $this->fontmanager->renderfontface();
        $this->routedirect('font');
    }

    public function addfont()
    {
        
    }
}


?>