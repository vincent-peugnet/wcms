<?php

namespace Wcms;

use RuntimeException;

class Controllerprofile extends Controller
{
    public function desktop()
    {
        if ($this->user->isinvite()) {
            $datas['user'] = $this->usermanager->get($this->user);
            $this->showtemplate('profile', $datas);
        } else {
            $this->routedirect('home');
        }
    }

    public function update()
    {
        if ($this->user->isinvite()) {
            $user = $this->usermanager->get($this->user);
            try {
                $user->hydrateexception($_POST);
            } catch (RuntimeException $th) {
                Model::sendflashmessage('There was a problem when updating preference : ' . $th->getMessage(), 'error');
            }
            $this->usermanager->add($user);
            $this->routedirect('profile');
        } else {
            $this->routedirect('home');
        }
    }

    public function password()
    {
        if ($this->user->isinvite()) {
            if (
                !isset($_POST['currentpassword']) ||
                !$this->usermanager->passwordcheck($this->user->id(), $_POST['currentpassword'])
            ) {
                Model::sendflashmessage("wrong current password", 'error');
                $this->routedirect('profile');
            }

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
            $this->routedirect('profile');
        } else {
            $this->routedirect('home');
        }
    }
}
