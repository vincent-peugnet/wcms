<?php

class Controllerconnect extends Controller
{

    public function log()
    {
        if (isset($_POST['log'])) {
            if (isset($_POST['id'])) {
                $id = $_POST['id'];
            } else {
                $id = null;
            }
            if ($_POST['log'] === 'login') {
                $this->login($id);
            } elseif ($_POST['log'] === 'logout') {
                $this->logout($id);
            }
        }

    }


    public function connect()
    {
        $this->showtemplate('connect', []);
    }





    public function login($id)
    {
        if (isset($_POST['pass'])) {
            $this->user = $this->usermanager->login($_POST['pass']);
            if($this->user != false) {
                $this->usermanager->writesession($this->user);
                $_SESSION['workspace']['showleftpanel'] = true;
                $_SESSION['workspace']['showrightpanel'] = false;
                
            }
        }
        if (!empty($id)) {
            $this->routedirect('artread/', ['art' => $id]);
        } else {
            $this->routedirect('home');
        }
    }

    public function logout($id)
    {
        $this->user = $this->usermanager->logout();
        $this->usermanager->writesession($this->user);
        if (!empty($id)) {
            $this->routedirect('artread/', ['art' => $id]);
        } else {
            $this->routedirect('home');
        }
    }



}






?>