<?php

namespace Wcms;

class Mediaopt extends Item
{
    /** @var string With a `/media` at the beginning and no trailing slash */
    protected string $path;

    /** @var string */
    protected string $sortby = 'filename';

    /** @var int */
    protected int $order = 1;

    /** @var string[] list of media type to display */
    protected array $type = [];

    protected const FILTERLIST = [
        'type',
    ];

    // ______________________________________________ F U N ________________________________________________________



    /**
     * @param object|array<string, mixed> $datas
     */
    public function __construct($datas = [])
    {
        $this->path = "/" . rtrim(Model::MEDIA_DIR, "/");
        $this->hydrate($datas);
    }

    /**
     * Generate link address for table header
     *
     * @param string $sortby
     * @return string link address
     */
    public function getsortbyaddress(string $sortby): string
    {
        if (!in_array($sortby, Modelmedia::MEDIA_SORTBY)) {
            $sortby = 'id';
        }
        if ($this->sortby === $sortby) {
            $order = $this->order * -1;
        } else {
            $order = $this->order;
        }
        $query = ['path' => $this->path, 'type' => $this->type, 'sortby' => $sortby, 'order' => $order];
        return '?' . urldecode(http_build_query($query));
    }

    /**
     * Give the GET params to be used for redirection. Using hidden input under the `route` name.
     *
     * @param string $path                  Media path to display. Default is the current path.
     * @return string                       URL-encoded path, filter and sort parameters, startiting with a `?`
     */
    public function getpathaddress(?string $path = null): string
    {
        $path = is_null($path) ? $this->path : "/$path";
        $query = ['path' => $path, 'sortby' => $this->sortby, 'order' => $this->order];
        if (array_diff(Media::mediatypes(), $this->type) != []) {
            $query['type'] = $this->type;
        }
        return '?' . urldecode(http_build_query($query));
    }

    /**
     * @return bool indicating if any filters are actives
     */
    public function isfiltered(): bool
    {
        $defaultvarlist = get_class_vars(self::class);
        foreach (self::FILTERLIST as $var) {
            if ($this->$var !== $defaultvarlist[$var]) {
                return true;
            }
        }
        return false;
    }

    /**
     * Params used for reset filtering button
     * Keep sortby and order settings, just reset the filtering options
     *
     * @return string                       URL encoded options without filtering, starting with `?`
     */
    public function getresetparams(): string
    {
        $defaultvars = get_class_vars(self::class);
        $params = get_object_vars($this);
        foreach (self::FILTERLIST as $var) {
            $params[$var] = $defaultvars[$var];
        }
        return '?' . urldecode(http_build_query($params));
    }


    // ___________________ MAGIC FOLDERS _____________________


    public function isfontdir(): bool
    {
        return $this->dir() === Model::FONT_DIR;
    }

    public function iscssdir(): bool
    {
        return $this->dir() === Model::CSS_DIR;
    }

    public function isthumbnaildir(): bool
    {
        return $this->dir() === Model::THUMBNAIL_DIR;
    }

    public function isfavicondir(): bool
    {
        return $this->dir() === Model::FAVICON_DIR;
    }

    // ______________________________________________ G E T ________________________________________________________


    /**
     * @return string formated like `/media/<folder>`
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * @return string formated like `media/<folder>/`
     */
    public function dir(): string
    {
        return trim($this->path, '/') . '/';
    }

    public function sortby(): string
    {
        return $this->sortby;
    }

    public function order(): int
    {
        return $this->order;
    }

    /**
     * @return string[]
     */
    public function type(): array
    {
        return $this->type;
    }

    // ______________________________________________ S E T ________________________________________________________


    /**
     * @param string $path
     */
    public function setpath(string $path): void
    {
        // gather nested slashs
        $path = preg_replace("%\/{2,}%", "/", $path);
        $this->path = "/" . trim($path, "/");
    }

    public function setsortby(string $sortby): void
    {
        if (in_array($sortby, Modelmedia::MEDIA_SORTBY)) {
            $this->sortby = $sortby;
        }
    }

    public function setorder(int $order): void
    {
        if ($order === -1 || $order === 1) {
            $this->order = $order;
        }
    }

    /**
     * @param string[] $type
     */
    public function settype(array $type): void
    {
        $this->type = array_intersect(Media::mediatypes(), array_unique($type));
        if (array_diff(Media::mediatypes(), $this->type) === []) {
            $this->type = [];
        }
    }
}
