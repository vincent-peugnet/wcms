<?php

namespace Wcms;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use RuntimeException;

class Bookmark extends Item
{
    /** @var string $id Bookmark ID */
    protected $id;
    /** @var string $query */
    protected $query = '';
    /** @var string $route Can be `page|media` */
    protected $route;
    /** @var array $params*/
    protected $params = [];
    /** @var string $icon associated emoji */
    protected $icon = '⭐';


    public function __construct(array $datas = [])
    {
        $this->hydrate($datas);
    }

    public function init(string $id, string $route, string $query, array $params = [], string $icon = '⭐')
    {
        $this->setid($id);
        $this->setroute($route);
        $this->setquery($query);
        $this->setparams($params);
        $this->seticon($icon);
    }



    
    // _____________________________ G E T __________________________________


    public function id()
    {
        return $this->id;
    }

    public function query()
    {
        return $this->query;
    }

    public function route()
    {
        return $this->route;
    }

    public function params()
    {
        return $this->params;
    }

    public function icon()
    {
        return $this->icon;
    }

    // _____________________________ S E T __________________________________

    public function setid($id)
    {
        if (is_string($id)) {
            $this->id = idclean($id);
        }
    }

    public function setquery($query)
    {
        if (is_string($query)) {
            $this->query = substr($query, 0, Model::MAX_QUERY_LENGH);
        }
    }

    public function setroute($route)
    {
        if ($route === 'home' || $route === 'media') {
            $this->route = $route;
        }
    }

    public function setparams($params)
    {
        if (is_array($params)) {
            $this->params = $params;
        }
    }

    public function seticon($icon)
    {
        if (is_string($icon)) {
            $this->icon = substr(strip_tags($icon), 0, 16);
        }
    }
}
