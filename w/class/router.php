<?php


class Router
{
    protected $route;

    const ROUTES = [
        'art' => ['art', 'read'],
        'art aff=read' => ['art', 'read'],
        'art aff=edit' => ['art', 'edit'],
        'art aff=log' => ['art', 'log'],
        'art action=update' => ['art', 'update'],
        'art action=update home' => ['art', 'update', 'home'],
        'art action=add' => ['art', 'add'],
        'art action=delete' => ['art', 'delete'],
        'aff=home action=massedit',
        'aff=home' => ['home', 'desktop'],
        '' => ['home', 'desktop'],
        'aff=home action=massedit' => ['home', 'massedit'],
        'action=massedit' => ['home', 'massedit'],
        'action=analyseall' => ['home', 'analyseall'],
        'aff=home action=analyseall' => ['home', 'analyseall'],
        'art action=login' => ['art', 'login', 'art'],
        'home action=login' => ['home', 'login', 'home'],
        'action=login' => ['home', 'login'],
        'art action=logout' => ['art', 'logout', 'art'],
        'home action=logout' => ['home', 'logout', 'home'],
        'action=logout' => ['home', 'logout'],
        'aff=db' => ['db', 'desktop'],
        'aff=db action=add' => ['db', 'add'],
        'aff=media' => ['media', 'desktop'],
        'aff=media action=addmedia' => ['media', 'addmedia'],
        'aff=admin' => ['admin', 'desktop'],
        'aff=co' => ['connect', 'desktop'],
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
        $controller = new $class($this->route->id());
        $params = array_slice($method, 2);
        $controller->$function(...$params);
    }



}






?>