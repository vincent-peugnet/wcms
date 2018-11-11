<?php

class Controllerhome extends Controller
{

    protected $modelhome;
    protected $opt;

    public function __construct($render) {
        parent::__construct($render);
        $this->modelhome = new Modelhome;
    }




    public function desktop()
    {

        $this->table2();
    }

    public function table2()
    {
        $table = $this->modelhome->getlister();
        $this->opt = $this->modelhome->optinit($table);

        $table2 = $this->modelhome->table2($table, $this->opt);

        $this->showtemplate('home', ['user' => $this->user, 'table2' => $table2, 'opt' =>$this->opt]);


    }

    public function analyseall()
    {
        if($this->user->level() >= Modeluser::EDITOR) {
            $scan = new Modelanalyse;
            $scan->analyseall();
            $this->redirect('./');

        }
    }

    public function massedit()
    {
        echo '<h2>Mass Edit</h2>';

    }







}








?>