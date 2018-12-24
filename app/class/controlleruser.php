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
            if(empty($user->id()) || $this->usermanager->get($user)) {
                $this->routedirectget('user', ['error' => 'wrong_id']);
            } elseif(empty($user->password()) || $this->usermanager->passwordexist($user->password())) {
                $this->routedirectget('user', ['error' => 'wrong_password']);
            } else {
                $this->usermanager->add($user);
                $this->routedirect('user');
            }
        }
    }

    public function update()
    {
        if($_POST['action'] === 'delete') {
            $user = new User($_POST);
            $this->showtemplate('userconfirmdelete', ['userdelete' => $user]);
        } elseif ($_POST['action'] == 'confirmdelete') {
            $user = new User($_POST);
            $this->usermanager->delete($user);
            $this->routedirect('user');
        }
    }
}



?>