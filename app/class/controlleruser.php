<?php

class Controlleruser extends Controller
{

    public function __construct($router) {
        parent::__construct($router);
    }

    public function desktop()
    {
        if($this->user->iseditor()) {
            $getuser = $this->usermanager->get($this->user);
            if($this->user->isadmin()) {
                $userlist = $this->usermanager->getlister();
                $this->showtemplate('user', ['userlist' => $userlist, 'getuser' => $getuser]);
            } else {
                $this->showtemplate('user', ['getuser' => $getuser]);
            }
        } else {
            $this->routedirect('home');
        }
    }


    public function pref()
    {
        if($this->user->iseditor()) {
            $user = $this->usermanager->get($this->user);
            $user->hydrate($_POST);
            $this->usermanager->add($user);
            $this->routedirect('user');
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
            } elseif(empty($user->password()) || $this->usermanager->passwordexist($user->password()) || !$user->validpassword()) {
                $this->routedirectget('user', ['error' => 'change_password']);
            } else {
                if($user->passwordhashed()) {
                    $user->hashpassword();
                }
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
                    $userupdate = clone $user;
                    $userupdate->hydrate($_POST);
                    if(empty($userupdate->id())) {
                        $this->routedirectget('user', ['error' => 'wrong_id']);
                    } elseif (!empty($_POST['password']) && (empty($userupdate->password())  || $this->usermanager->passwordexist($userupdate->password()) || !$userupdate->validpassword())) {
                        $this->routedirectget('user', ['error' => 'password_unvalid']);
                    } elseif (empty($userupdate->level())) {
                        $this->routedirectget('user', ['error' => 'wrong_level']);
                    } elseif ($user->level() === 10 && $userupdate->level() !== 10 && $this->user->id() === $user->id()) {
                        $this->routedirectget('user', ['error' => 'cant_edit_yourself']);
                    } else {
                        if($userupdate->password() !== $user->password() && $user->passwordhashed()) {
                            $userupdate->setpasswordhashed(false);
                        }
                        if($userupdate->passwordhashed() && !$user->passwordhashed()) {
                            $userupdate->hashpassword();
                        }
                        $this->usermanager->add($userupdate);
                        $this->routedirect('user');
                    }
            }
        } else {
            $this->routedirect('home');
        }
    }
}



?>