<?php

class Controllerhome extends Controller
{
    /** @var Modelhome */
    protected $modelhome;
    protected $opt;

    public function __construct($render) {
        parent::__construct($render);
        $this->modelhome = new Modelhome;
    }




    public function desktop()
    {

        $table = $this->modelhome->getlister();
        $this->opt = $this->modelhome->optinit($table);

        $table2 = $this->modelhome->table2($table, $this->opt);

        $this->showtemplate('home', ['user' => $this->user, 'table2' => $table2, 'opt' =>$this->opt]);


    }

    public function massedit()
    {
        echo '<h2>Mass Edit</h2>';

    }

    public function search()
    {
        if(isset($_POST['id']) && !empty($_POST['id'])) {
            $this->routedirect('artread/', ['art' => $_POST['id']]);
        } else {
            $this->routedirect('home');
        }
    }







}








?>