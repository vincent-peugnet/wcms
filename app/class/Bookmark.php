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

    /**
     * @throws RuntimeException
     */
    public function __construct(array $datas = [])
    {
        $this->hydrate($datas, true);
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

    public function setid($id): bool
    {
        if (is_string($id)) {
            try {
                $this->id = idclean($id, Model::MAX_ID_LENGTH, 1);
            } catch (\Throwable $th) {
                return false;
            }
            return true;
        }
        return false;
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
            return true;
        } else {
            return false;
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
