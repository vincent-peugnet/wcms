<?php

class Controller extends Application
{

    protected $user;
    protected $usermanager;
    protected $templates;

	public function __construct() {
        parent::__construct();
        $this->setuser();        
        $this->settemplate();        
	}



    public function setuser()
    {
        $this->usermanager = new Modeluser;        
        $this->user = $this->usermanager->readsession();
    }

    public function settemplate()
    {
        $this->templates = new League\Plates\Engine(Model::TEMPLATES_DIR);
    }
    
    public function useriseditor()
    {
        if ($this->user->level() >= $this->usermanager::EDITOR) {
            echo '<h3>Editor access</h3>';
            return true;
        } else {
            echo '<h3>Not enought rights to see more...</h3>';
            return false;
        }
    }





    public function redirect($url)
    {
        header('Location: ' . $url);
    }



}





?>