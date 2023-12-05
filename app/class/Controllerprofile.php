<?php

namespace Wcms;

use RuntimeException;
use Wcms\Exception\Databaseexception;
use Wcms\Exception\Database\Notfoundexception;

class Controllerprofile extends Controller
{
    public function __construct($router)
    {
        parent::__construct($router);

        if ($this->user->isvisitor()) {
            http_response_code(401);
            $this->showtemplate('connect', ['route' => 'profile']);
            exit;
        }
    }

    public function desktop()
    {
        try {
            $datas['user'] = $this->usermanager->get($this->user);
            $this->showtemplate('profile', $datas);
        } catch (Notfoundexception $e) {
            Model::sendflashmessage($e->getMessage(), Model::FLASH_ERROR);
            $this->routedirect('home');
        }
    }

    public function update()
    {
        try {
            $user = $this->usermanager->get($this->user);
            $user->hydrateexception($_POST);
            $this->usermanager->update($user);
            Model::sendflashmessage('Successfully updated', Model::FLASH_SUCCESS);
        } catch (Notfoundexception $e) {
            Model::sendflashmessage($e->getMessage(), Model::FLASH_ERROR);
        } catch (RuntimeException $e) {
            Model::sendflashmessage(
                'There was a problem when updating preference : ' . $e->getMessage(),
                Model::FLASH_ERROR
            );
        }
        $this->routedirect('profile');
    }

    /**
     * Update the user's password.
     */
    public function password()
    {
        if (
            !isset($_POST['currentpassword']) ||
            !$this->usermanager->passwordcheck($this->user, $_POST['currentpassword'])
        ) {
            Model::sendflashmessage("wrong current password", 'error');
            $this->routedirect('profile');
        }

        if (
            empty($_POST['password1']) ||
            empty($_POST['password2']) ||
            $_POST['password1'] !== $_POST['password2']
        ) {
            Model::sendflashmessage("passwords does not match", Model::FLASH_ERROR);
            $this->routedirect('profile');
        }

        if (
            !$this->user->setpassword($_POST['password1']) ||
            !$this->user->hashpassword()
        ) {
            Model::sendflashmessage("password is not compatible", Model::FLASH_ERROR);
            $this->routedirect('profile');
        }

        try {
            $this->usermanager->add($this->user);
            Model::sendflashmessage('password updated successfully', Model::FLASH_SUCCESS);
        } catch (Databaseexception $e) {
            Model::sendflashmessage($e->getMessage(), Model::FLASH_ERROR);
        }
        $this->routedirect('profile');
    }
}
