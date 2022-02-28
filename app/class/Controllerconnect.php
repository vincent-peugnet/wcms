<?php

namespace Wcms;

use RuntimeException;

class Controllerconnect extends Controller
{
    /** @var Modelconnect */
    protected $modelconnect;

    public function log()
    {
        if (isset($_POST['log'])) {
            $id = $_POST['id'] ?? null;
            $route = $_POST['route'] ?? 'home';
            if ($_POST['log'] === 'login') {
                $this->login($route, $id);
            } elseif ($_POST['log'] === 'logout') {
                $this->logout($route, $id);
            }
        }
    }


    public function connect()
    {
        if (isset($_SESSION['pageupdate'])) {
            $pageupdate['route'] = 'pageedit';
            $pageupdate['id'] = $_SESSION['pageupdate']['id'];
        } else {
            $pageupdate = ['route' => 'home'];
        }
        $this->showtemplate('connect', $pageupdate);
    }





    public function login($route, $id = null)
    {
        if (!empty($_POST['pass']) && !empty($_POST['user'])) {
            $this->user = $this->usermanager->passwordcheck($_POST['user'], $_POST['pass']);
            if (
                $this->user != false
                && (
                    $this->user->expiredate() === false
                    || $this->user->level() === 10
                    || $this->user->expiredate('date') > $this->now
                )
            ) {
                $this->user->connectcounter();
                $this->usermanager->add($this->user);
                $this->session->addtosession('user', $this->user->id());

                if (!empty($_POST['rememberme'])) {
                    if ($this->user->cookie() > 0) {
                        try {
                            $this->modelconnect = new Modelconnect();
                            $wsession = $this->user->newsession();
                            $this->modelconnect->createauthcookie(
                                $this->user->id(),
                                $wsession,
                                $this->user->cookie()
                            );
                            $this->usermanager->add($this->user);
                            $this->session->addtosession('wsession', $wsession);
                        } catch (RuntimeException $e) {
                            Model::sendflashmessage("Can't create authentification cookie : $e", "warning");
                        }
                    } else {
                        $message = "Can't remember you beccause user cookie conservation time is set to 0 days";
                        Model::sendflashmessage($message, "warning");
                    }
                }
            }
        }
        if ($id !== null) {
            $this->routedirect($route, ['page' => $id]);
        } else {
            $this->routedirect($route);
        }
    }

    public function logout($route, $id = null)
    {
        $this->session->addtosession('user', '');
        $this->user->destroysession($this->session->wsession);
        $this->session->addtosession('wsession', '');
        $this->usermanager->add($this->user);

        if ($id !== null && $route !== 'home') {
            $this->routedirect($route, ['page' => $id]);
        } else {
            $this->routedirect($route);
        }
    }
}
