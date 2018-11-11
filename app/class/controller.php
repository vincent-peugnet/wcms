<?php

class Controller
{

    protected $user;
    protected $router;
    protected $usermanager;
    protected $plates;

	public function __construct($router) {
        $this->setuser();
        $this->router = $router;        
        $this->initplates();       
        $this->initconfig(); 
	}

    public function setuser()
    {
        $this->usermanager = new Modeluser;        
        $this->user = $this->usermanager->readsession();
    }

    public function initplates()
    {
        $router = $this->router;
        $this->plates = new League\Plates\Engine(Model::TEMPLATES_DIR);
        $this->plates->registerFunction('url', function (string $string, array $vars = []) use ($router) {
            return $router->generate($string, $vars);
        });
        $this->plates->registerFunction('uart', function (string $string, string $id) use ($router) {
            return $router->generate($string, ['art' => $id]);
        });
    }
    
    public function useriseditor()
    {
        if ($this->user->level() >= $this->usermanager::EDITOR) {
            echo '<h3>Editor access</h3>';
            return true;
        } else {
            echo '<h3>Not enought rights to see more...</h3>';
            return false;
        }
    }

    public function initconfig()
    {
        Config::readconfig();
    }

    public function showtemplate($template, $params)
    {
        $params = array_merge($this->commonsparams(), $params);
        echo $this->plates->render($template, $params);
    }

    public function commonsparams()
    {
        $commonsparams = [];
        $commonsparams['router'] = $this->router;
        $commonsparams['user'] = $this->user;
        $commonsparams['css'] = Model::csspath();
        return $commonsparams;
    }

    public function login($redirect = 'home')
    {
        if(isset($_POST['pass'])) {
            $this->user = $this->usermanager->login($_POST['pass']);
            $this->usermanager->writesession($this->user);
        }
        if($redirect == 'art') {
            $this->redirect('?id=' . $this->art->id());
        } else {
            $this->redirect('?aff=' . $redirect);
        }
    }

    public function logout($redirect = 'home')
    {
        $this->user = $this->usermanager->logout();
        $this->usermanager->writesession($this->user);
        if($redirect == 'art') {
            $this->redirect('?id=' . $this->art->id());
        } else {
            $this->redirect('?aff=' . $redirect);
        }
    }




    public function redirect($url)
    {
        header('Location: ' . $url);
    }

    public function routedirect(string $route, array $vars = [])
    {
        $this->redirect($this->router->generate($route, $vars));
    }



}





?>