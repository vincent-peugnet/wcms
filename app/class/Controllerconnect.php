<?php

namespace Wcms;

use RuntimeException;

class Controllerconnect extends Controller
{
    /** @var Modelconnect */
    protected $modelconnect;

    public function log(): void
    {
        if (isset($_POST['log'])) {
            $id = $_POST['id'] ?? null;
            $route = $_POST['route'] ?? 'home';
            if ($_POST['log'] === 'login') {
                $this->login();
            } elseif ($_POST['log'] === 'logout') {
                $this->logout();
            }
            if (is_string($id)) {
                $this->routedirect($route, ['page' => $id]);
            } else {
                $this->routedirect($route);
            }
        }
    }


    public function connect(): never
    {
        if (isset($_SESSION['pageupdate'])) {
            $this->showconnect('pageedit', $_SESSION['pageupdate']['id']);
        } else {
            $this->showconnect('home');
        }
    }

    protected function login(): void
    {
        if (!isset($_POST['user']) || !isset($_POST['pass'])) {
            return;
        }

        $username = strval($_POST['user']);
        $password = strval($_POST['pass']);

        try {
            $user = $this->connectmanager->login($username, $password);
            $this->servicesession->setuser($user->id());
            $this->sendflashmessage("Successfully logged in as " . $user->id(), self::FLASH_SUCCESS);
            Logger::info("successful login for user " . $user->id());
        } catch (RuntimeException $e) {
            $this->sendflashmessage($e->getMessage(), self::FLASH_ERROR);
            Logger::error("failed to login user $username:" . $e->getMessage());
            return;
        }

        if (isset($_POST['rememberme']) && $_POST['rememberme'] == 1) {
            try {
                $wsessionid = $this->connectmanager->remember($user);
                $this->servicesession->setwsessionid($wsessionid); // Used to destroy session from User
            } catch (RuntimeException $e) {
                $this->sendflashmessage($e->getMessage(), self::FLASH_WARNING);
                Logger::warning("failed to remember user $username:" . $e->getMessage());
            }
        }
    }

    protected function logout(): void
    {
        $this->disconnect();
    }
}
