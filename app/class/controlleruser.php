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
                $this->routedirectget('user', ['error' => 'change_password']);
            } else {
                $this->usermanager->add($user);
                $this->routedirect('user');
            }
        }
    }

    public function update()
    {
        if($this->user->isadmin() && isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'delete':
                    $user = new User($_POST);
                    $user = $this->usermanager->get($user);
                    if($user !== false) {
                        if($user->id() === $this->user->id()) {
                            $this->showtemplate('userconfirmdelete', ['userdelete' => $user, 'candelete' => false]);
                        } else {
                            $this->showtemplate('userconfirmdelete', ['userdelete' => $user, 'candelete' => true]);
                        }
                    } else {
                        $this->routedirect('user');
                    }
                    break;
                
                case 'confirmdelete':
                    $user = new User($_POST);
                    $this->usermanager->delete($user);
                    $this->routedirect('user');
                    break;

                case 'update':
                    $user = $this->usermanager->get($_POST['id']);
                    $user->hydrate($_POST);
                    if(empty($user->id())) {
                        $this->routedirectget('user', ['error' => 'wrong_id']);
                    } elseif (empty($user->password())  | $this->usermanager->passwordexist($user->password())) {
                        $this->routedirectget('user', ['error' => 'change_password']);
                    } elseif (empty($user->level())) {
                        $this->routedirectget('user', ['error' => 'wrong_level']);
                    } else {
                        $this->usermanager->add($user);
                        $this->routedirect('user');
                    }
            }
        } else {
            $this->routedirect('home');
        }
    }
}



?>