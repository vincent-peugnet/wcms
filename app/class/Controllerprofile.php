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
            $this->sendflashmessage($e->getMessage(), self::FLASH_ERROR);
            $this->routedirect('home');
        }
    }

    public function update()
    {
        try {
            $user = $this->usermanager->get($this->user);
            $user->hydrateexception($_POST);
            $this->usermanager->update($user);
            $this->sendflashmessage('Successfully updated', self::FLASH_SUCCESS);
        } catch (Notfoundexception $e) {
            $this->sendflashmessage($e->getMessage(), self::FLASH_ERROR);
        } catch (RuntimeException $e) {
            $this->sendflashmessage(
                'There was a problem when updating preference : ' . $e->getMessage(),
                self::FLASH_ERROR
            );
        }
        $this->routedirect('profile');
    }

    /**
     * Update the user's password.
     */
    public function password()
    {
        if ($this->user->isldap()) {
            http_response_code(403);
            $this->showtemplate('forbidden', ['route' => 'profile']);
            exit;
        }

        if (
            !isset($_POST['currentpassword']) ||
            !$this->usermanager->passwordcheck($this->user, $_POST['currentpassword'])
        ) {
            $this->sendflashmessage("wrong current password", self::FLASH_ERROR);
            $this->routedirect('profile');
        }

        if (
            empty($_POST['password1']) ||
            empty($_POST['password2']) ||
            $_POST['password1'] !== $_POST['password2']
        ) {
            $this->sendflashmessage("passwords does not match", self::FLASH_ERROR);
            $this->routedirect('profile');
        }

        if (
            !$this->user->setpassword($_POST['password1']) ||
            !$this->user->hashpassword()
        ) {
            $this->sendflashmessage("password is not compatible", self::FLASH_ERROR);
            $this->routedirect('profile');
        }

        try {
            $this->usermanager->add($this->user);
            $this->sendflashmessage('password updated successfully', self::FLASH_SUCCESS);
        } catch (Databaseexception $e) {
            $this->sendflashmessage($e->getMessage(), self::FLASH_ERROR);
        }
        $this->routedirect('profile');
    }
}
