<?php

class Controlleruser extends Controller
{

    public function __construct($render) {
        parent::__construct($render);
    }

    public function desktop()
    {
        if($this->user->isadmin()) {
            $userlist = $this->usermanager->getlister();
            $this->showtemplate('user', ['userlist' => $userlist]);
        } else {
            $this->routedirect('home');
        }
    }

    public function add()
    {
        if(isset($_POST['id'])) {
            $user = new User($_POST);
            if(!$this->usermanager->get($user)) {
                $this->usermanager->add($user);
            }
        }
    }
}



?>