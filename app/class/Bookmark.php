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

    /** @var array $params*/
    protected $params = [];

    /** @var string $icon associated emoji */
    protected $icon = '⭐';

    /** @var string $user user owning the bookmark */
    protected string $user = '';

    /** @var bool $published Indicate if the bookmark is also a RSS feed */
    protected bool $published = false;

    /** @var string ID of reference page */
    protected string $ref = "";

    /**
     * @throws RuntimeException
     */
    public function __construct($datas = [])
    {
        $this->hydrateexception($datas);
    }

    public function init(
        string $id,
        string $query,
        string $icon = '⭐',
        string $name = '',
        string $description = ''
    ) {
        $this->setid($id);
        $this->setquery($query);
        $this->seticon($icon);
        $this->setname($name);
        $this->setdescription($description);
    }

    public function ispublic(): bool
    {
        return empty($this->user);
    }

    public function ispublished(): bool
    {
        return $this->published;
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

    public function params()
    {
        return $this->params;
    }

    public function icon()
    {
        return $this->icon;
    }

    public function user(): string
    {
        return $this->user;
    }

    public function published(): bool
    {
        return $this->published;
    }

    public function ref(): ?string
    {
        return $this->ref;
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
            $this->query = strip_tags(substr($query, 0, Model::MAX_QUERY_LENGH));
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

    public function setuser($user)
    {
        if (is_string($user)) {
            $this->user = Model::idclean($user);
            return true;
        }
        return false;
    }

    public function setpublished(bool $published)
    {
        $this->published = $published;
    }

    /**
     * @param string $id                    ID of reference page
     */
    public function setref(string $id): void
    {
        if (!empty($id) && !Model::idcheck($id)) {
            $this->ref = "";
        }
        $this->ref = $id;
    }
}
