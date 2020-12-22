<?php

namespace Wcms;

use RuntimeException;

class Controlleruser extends Controller
{

    public function __construct($router)
    {
        parent::__construct($router);
    }

    public function desktop()
    {
        if ($this->user->iseditor()) {
            $authtokenmanager = new Modelauthtoken();
            $datas['tokenlist'] = $authtokenmanager->listbyuser($this->user->id());
            $datas['getuser'] = $this->usermanager->get($this->user);

            if ($this->user->isadmin()) {
                $datas['userlist'] = $this->usermanager->getlister();
                $this->showtemplate('user', $datas);
            } else {
                $this->showtemplate('user', $datas);
            }
        } else {
            $this->routedirect('home');
        }
    }


    public function pref()
    {
        if ($this->user->iseditor()) {
            $user = $this->usermanager->get($this->user);
            try {
                $user->hydrateexception($_POST);
            } catch (RuntimeException $th) {
                Model::sendflashmessage('There was a problem when updating preference : ' . $th->getMessage(), 'error');
            }
            $this->usermanager->add($user);
            $this->routedirect('user');
        } else {
            $this->routedirect('home');
        }
    }

    public function password()
    {
        if ($this->user->iseditor()) {
            if (
                !empty($_POST['password1']) &&
                !empty($_POST['password2']) &&
                $_POST['password1'] === $_POST['password2']
            ) {
                if (
                    $this->user->setpassword($_POST['password1']) &&
                    $this->user->hashpassword() &&
                    $this->usermanager->add($this->user)
                ) {
                    Model::sendflashmessage('password updated successfully', 'success');
                } else {
                    Model::sendflashmessage("password is not compatible or an error occured", 'error');
                }
            } else {
                Model::sendflashmessage("passwords does not match", "error");
            }
            $this->routedirect('user');
        } else {
            $this->routedirect('home');
        }
    }


    public function bookmark()
    {
        if ($this->user->iseditor() && isset($_POST['action']) && isset($_POST['id']) && !empty($_POST['id'])) {
            if ($_POST['action'] == 'add' && isset($_POST['query'])) {
                if (isset($_POST['user']) && $_POST['user'] == $this->user->id()) {
                    try {
                        $bookmark = new Bookmark($_POST);
                        $usermanager = new Modeluser();
                        $user = $usermanager->get($_POST['user']);
                        $user->addbookmark($bookmark);
                        $usermanager->add($user);
                    } catch (RuntimeException $th) {
                        Logger::errorex($th, true);
                        Model::sendflashmessage('Error while creating bookmark : ' . $th->getMessage(), 'error');
                    }
                }
            } elseif ($_POST['action'] == 'del') {
                if (isset($_POST['user']) && $_POST['user'] == $this->user->id()) {
                    $usermanager = new Modeluser();
                    $user = $usermanager->get($_POST['user']);
                    foreach ($_POST['id'] as $id) {
                        $user->deletebookmark($id);
                    }
                    $usermanager->add($user);
                }
            }
        }
        $this->routedirect($_POST['route']);
    }






    public function add()
    {
        if (isset($_POST['id'])) {
            $user = new User($_POST);
            if (empty($user->id()) || $this->usermanager->get($user)) {
                $this->routedirectget('user', ['error' => 'wrong_id']);
            } elseif (empty($user->password()) || !$user->validpassword()) {
                $this->routedirectget('user', ['error' => 'change_password']);
            } else {
                if ($user->passwordhashed()) {
                    $user->hashpassword();
                }
                $this->usermanager->add($user);
                $this->routedirect('user');
            }
        }
    }

    public function token()
    {
        if (isset($_POST['tokendelete'])) {
            $authtokenmanager = new Modelauthtoken();
            $authtokenmanager->delete($_POST['tokendelete']);
        }
        $this->routedirect('user');
    }

    public function update()
    {
        if ($this->user->isadmin() && isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'delete':
                    $user = new User($_POST);
                    $user = $this->usermanager->get($user);
                    if ($user !== false) {
                        if ($user->id() === $this->user->id()) {
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
                    if (empty($userupdate->id())) {
                        $this->routedirectget('user', ['error' => 'wrong_id']);
                    } elseif (
                        !empty($_POST['password'])
                        && (empty($userupdate->password())
                        || !$userupdate->validpassword())
                    ) {
                        $this->routedirectget('user', ['error' => 'password_unvalid']);
                    } elseif (empty($userupdate->level())) {
                        $this->routedirectget('user', ['error' => 'wrong_level']);
                    } elseif (
                        $user->level() === 10
                        && $userupdate->level() !== 10
                        && $this->user->id() === $user->id()
                    ) {
                        $this->routedirectget('user', ['error' => 'cant_edit_yourself']);
                    } else {
                        if ($userupdate->password() !== $user->password() && $user->passwordhashed()) {
                            $userupdate->setpasswordhashed(false);
                        }
                        if ($userupdate->passwordhashed() && !$user->passwordhashed()) {
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
