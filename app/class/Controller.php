<?php

namespace Wcms;

use DateTime;
use DateTimeImmutable;
use Exception;
use InvalidArgumentException;
use League\Plates\Engine;

class Controller
{
    /** @var User */
    protected $user;

    /** @var \AltoRouter */
    protected $router;

    /** @var Modeluser */
    protected $usermanager;

    /** @var Modelpage */
    protected $pagemanager;

    protected $plates;
    
    /** @var DateTimeImmutable */
    protected $now;

    public function __construct($router)
    {
        $this->setuser();
        $this->router = $router;
        $this->pagemanager = new Modelpage();
        $this->initplates();
        $this->now = new DateTimeImmutable("now", timezone_open("Europe/Paris"));
    }

    public function setuser()
    {
        $this->usermanager = new Modeluser();
        $this->user = $this->usermanager->readsession();
    }

    public function initplates()
    {
        $router = $this->router;
        $this->plates = new Engine(Model::TEMPLATES_DIR);
        $this->plates->registerFunction('url', function (string $string, array $vars = [], string $get = '') {
            return $this->generate($string, $vars, $get);
        });
        $this->plates->registerFunction('upage', function (string $string, string $id) {
            return $this->generate($string, ['page' => $id]);
        });
        $this->plates->addData(['flashmessages' => Model::getflashmessages()]);
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
        $commonsparams['pagelist'] = $this->pagemanager->list();
        $commonsparams['css'] = Model::assetscsspath();
        $commonsparams['now'] = new DateTimeImmutable();
        return $commonsparams;
    }



    /**
     * Generate the URL for a named route. Replace regexes with supplied parameters.
     *
     * @param string $route The name of the route.
     * @param array $params Associative array of parameters to replace placeholders with.
     * @param string $get Optionnal query GET parameters formated
     * @return string The URL of the route with named parameters in place.
     * @throws InvalidArgumentException If the route does not exist.
     */
    public function generate(string $route, array $params = [], string $get = ''): string
    {
        try {
            return $this->router->generate($route, $params) . $get;
        } catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function redirect($url)
    {
        header('Location: ' . $url);
    }

    public function routedirect(string $route, array $vars = [])
    {
        $this->redirect($this->generate($route, $vars));
    }

    public function routedirectget(string $route, array $vars = [])
    {
        $get = '?';
        foreach ($vars as $key => $value) {
            $get .= $key . '=' . $value . '&';
        }
        $get = rtrim($get, '&');
        $this->redirect($this->generate($route, []) . $get);
    }

    public function error(int $code)
    {
        http_response_code($code);
        exit;
    }

    /**
     *
     */
    public function sendstatflashmessage(int $count, int $total, string $message)
    {
        if ($count === $total) {
            Model::sendflashmessage($count . ' / ' . $total . ' ' . $message, 'success');
        } elseif ($count > 0) {
            Model::sendflashmessage($count . ' / ' . $total . ' ' . $message, 'warning');
        } else {
            Model::sendflashmessage($count . ' / ' . $total . ' ' . $message, 'error');
        }
    }
}
