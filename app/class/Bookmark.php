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

    /** @var string $icon associated emoji */
    protected $icon = '⭐';

    /** @var string $user user owning the bookmark */
    protected string $user = '';

    /** @var bool $published Indicate if the bookmark is also a RSS feed */
    protected bool $published = false;

    /** @var string ID of reference page */
    protected string $ref = "";

    /**
     * @param object|array<string, mixed> $datas
     *
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
    ): void {
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

    /**
     * @return array<string, mixed> Assoc array representing the query
     */
    public function querydata(): array
    {
        parse_str(ltrim($this->query, "?"), $data);
        return $data;
    }



    // _____________________________ G E T __________________________________


    public function id(): string
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

    public function query(): string
    {
        return $this->query;
    }

    public function icon(): string
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

    public function setid(string $id): bool
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

    public function setquery(string $query): void
    {
        $this->query = strip_tags(mb_substr($query, 0, Model::MAX_QUERY_LENGH));
    }

    public function seticon(string $icon): void
    {
        $this->icon = mb_substr(strip_tags($icon), 0, 16);
    }

    public function setuser(string $user): void
    {
        if (Model::idcheck($user)) {
            $this->user = $user;
        }
    }

    public function setpublished(bool $published): void
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
