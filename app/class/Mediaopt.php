<?php

namespace Wcms;

class Mediaopt extends Item
{
    /** @var string With a `/media` at the beginning and no trailing slash */
    protected $path;

    /** @var string */
    protected $sortby = 'filename';

    /** @var int */
    protected $order = 1;

    /** @var array list of media type to display */
    protected $type = [];



    // ______________________________________________ F U N ________________________________________________________



    public function __construct(array $datas = [])
    {
        $this->path = "/" . rtrim(Model::MEDIA_DIR, "/");
        $this->type = Media::mediatypes();
        $this->hydrate($datas);
    }

    /**
     * Generate link adress for table header
     *
     * @param string $sortby
     * @return string link adress
     */
    public function getsortbyadress(string $sortby): string
    {
        if (!in_array($sortby, Modelmedia::MEDIA_SORTBY)) {
            $sortby = 'id';
        }
        if ($this->sortby === $sortby) {
            $order = $this->order * -1;
        } else {
            $order = $this->order;
        }
        $query = ['path' => $this->path, 'sortby' => $sortby, 'order' => $order];
        if (array_diff(Media::mediatypes(), $this->type) != []) {
            $query['type'] = $this->type;
        }
        return '?' . urldecode(http_build_query($query));
    }

    /**
     * Give the GET params to be used for redirection. Using hidden input under the `route` name.
     *
     * @param string $path                  Media path to display. Default is the current path.
     * @return string                       URL-encoded path, filter and sort parameters, startiting with a `?`
     */
    public function getpathadress(string $path = null): string
    {
        $path = is_null($path) ? $this->path : "/$path";
        $query = ['path' => $path, 'sortby' => $this->sortby, 'order' => $this->order];
        if (array_diff(Media::mediatypes(), $this->type) != []) {
            $query['type'] = $this->type;
        }
        return '?' . urldecode(http_build_query($query));
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
    public function path()
    {
        return $this->path;
    }

    /**
     * @return string formated like `media/<folder>/`
     */
    public function dir()
    {
        return trim($this->path, '/') . '/';
    }

    public function sortby()
    {
        return $this->sortby;
    }

    public function order()
    {
        return $this->order;
    }

    public function type()
    {
        return $this->type;
    }

    // ______________________________________________ S E T ________________________________________________________


    /**
     * @param string $path
     */
    public function setpath(string $path)
    {
        // gather nested slashs
        $path = preg_replace("%\/{2,}%", "/", $path);
        $this->path = "/" . trim($path, "/");
    }

    public function setsortby(string $sortby)
    {
        if (in_array($sortby, Modelmedia::MEDIA_SORTBY)) {
            $this->sortby = $sortby;
        }
    }

    public function setorder(int $order)
    {
        if ($order === -1 || $order === 1) {
            $this->order = $order;
        }
    }

    public function settype($type)
    {
        if (is_array($type)) {
            $this->type = array_intersect(Media::mediatypes(), array_unique($type));
        }
    }
}
