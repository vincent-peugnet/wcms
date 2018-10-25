<?php


class Router
{
    protected $route;

    const ROUTES = [
        'art' => ['art', 'read'],
        'art aff=read' => ['art', 'read'],
        'art aff=edit' => ['art', 'edit'],
        'art action=update home' => ['art', 'update', 'home'],
        'art action=add' => ['art', 'add'],
        'art action=delete' => ['art', 'delete'],
        'aff=home action=massedit',
        'aff=home' => ['home', 'desktop'],
        '' => ['home', 'desktop'],
        'aff=home action=massedit' => ['home', 'massedit'],
        'action=massedit' => ['home', 'massedit'],
        'art action=login' => ['art', 'login'],
        'home action=login' => ['home', 'login'],
        'action=login' => ['home', 'login'],
        'aff=media' => ['media', 'desktop'],
        'aff=media action=addmedia' => ['media', 'addmedia'],
    ];

    public function __construct() {
        if($this->matchroute()) {
            $this->callmethod();
        } else {
            echo '<h1>404 Error</h1>';
        }
    }

    public function matchroute()
    {
        $this->route = new route($_GET);
        $match = array_key_exists($this->route->tostring(), self::ROUTES);
        return $match;

    }

    public function callmethod()
    {
        $method = self::ROUTES[$this->route->tostring()];

        $class = 'controller' . $method[0];
        $function = $method[1];
        $controller = new $class($this->route->artid());
        $params = array_slice($method, 2);
        $controller->$function(...$params);
    }



}






?>