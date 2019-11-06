<?php

namespace Wcms;

class Controllerconnect extends Controller
{

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
        if(isset($_SESSION['pageupdate'])) {
            $pageupdate['route'] = 'pageedit';
            $pageupdate['id'] = $_SESSION['pageupdate']['id'];
        } else {
            $pageupdate = ['route' => 'home'];
        }
        $this->showtemplate('connect', $pageupdate);
    }





    public function login($route, $id = null)
    {
        if (isset($_POST['pass'])) {
            $this->user = $this->usermanager->passwordcheck($_POST['pass']);
            if($this->user != false) {
                if($this->user->expiredate() === false || $this->user->level() === 10 || $this->user->expiredate('date') > $this->now) {
                    $this->user->connectcounter();
                    $this->usermanager->add($this->user);
                    $this->usermanager->writesession($this->user);
                    $_SESSION['workspace']['showleftpanel'] = true;
                    $_SESSION['workspace']['showrightpanel'] = false;
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
        $this->user = $this->usermanager->logout();
        $this->usermanager->writesession($this->user);
        if ($id !== null && $route !== 'home') {
            $this->routedirect($route, ['page' => $id]);
        } else {
            $this->routedirect($route);
        }
    }



}






?>