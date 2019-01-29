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
        if($this->user->isvisitor() && Config::homepage() === 'redirect' && Config::homeredirect() !== null) {
            $this->routedirect('artread/', ['art' => Config::homeredirect()]);
        } else {

            
            $table = $this->modelhome->getlister();
            $this->opt = $this->modelhome->optinit($table);
            
            $table2 = $this->modelhome->table2($table, $this->opt);
            
            $columns = $this->modelhome->setcolumns($this->user->columns());
            
            $this->showtemplate('home', ['user' => $this->user, 'table2' => $table2, 'opt' =>$this->opt, 'columns' => $columns]);
            
            
        }
    }

    public function columns()
    {
        if(isset($_POST['columns']) && $this->user->iseditor()) {
            $user  =$this->usermanager->get($this->user->id());
            $user->hydrate($_POST);
            $this->usermanager->add($user);
            $this->usermanager->writesession($user);
        }
        $this->routedirect('home');
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