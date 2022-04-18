<?php

namespace Wcms;

use RuntimeException;

class Bookmark extends Item
{
    /** @var string $id Bookmark ID */
    protected $id;
    /** @var string $name Name to be displayed */
    protected string $name = '';
    /** @var string $description used in title */
    protected string $description = '';
    /** @var string $query */
    protected $query = '';
    /** @var string $route Can be `home|media` */
    protected $route;
    /** @var array $params*/
    protected $params = [];
    /** @var string $icon associated emoji */
    protected $icon = '⭐';

    /**
     * @throws RuntimeException
     */
    public function __construct($datas = [])
    {
        $this->hydrateexception($datas);
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

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
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
            $id = Model::idclean($id);
            if (!empty($id)) {
                $this->id = $id;
                return true;
            }
        }
        return false;
    }

    public function setname(string $name): bool
    {
        if (strlen($name) < self::LENGTH_SHORT_TEXT) {
            $this->name = strip_tags(trim($name));
            return true;
        } else {
            return false;
        }
    }

    public function setdescription(string $description): bool
    {
        if (strlen($description) < self::LENGTH_SHORT_TEXT) {
            $this->description = strip_tags(trim($description));
            return true;
        } else {
            return false;
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
