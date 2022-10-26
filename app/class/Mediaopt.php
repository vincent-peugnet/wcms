<?php

namespace Wcms;

class Mediaopt extends Item
{
    /** @var string full regex match */
    protected $fullmatch;

    /** @var string full options code line */
    protected $options = '';

    /** @var string directory of media */
    protected $path = "/" . Model::MEDIA_DIR;

    /** @var string */
    protected $sortby = 'id';

    /** @var int */
    protected $order = 1;

    /** @var array list of media type to display */
    protected $type = [];

    /** @var int display the file name of the file */
    protected int $filename = 0;



    // ______________________________________________ F U N ________________________________________________________



    public function __construct(array $datas = [])
    {
        $this->type = Media::mediatypes();
        $this->hydrate($datas);
    }

    public function readoptions()
    {
        parse_str($this->options, $datas);
        $this->hydrate($datas);
    }

    public function generatecontent()
    {
        $mediamanager = new Modelmedia();
        $medialist = $mediamanager->getlistermedia($this->dir(), $this->type);
        if (!$medialist) {
            return false;
        } else {
            $mediamanager->medialistsort($medialist, $this->sortby, $this->order);

            $dirid = str_replace('/', '-', $this->path);

            $div = "<div class=\"medialist\" id=\"$dirid\">\n";

            foreach ($medialist as $media) {
                $div .= '<div class="content ' . $media->type() . '">';
                $id = 'id="media_' . $media->id() . '"';
                $path = $media->getincludepath();
                $ext = $media->extension();
                $filename = $media->id() . '.' . $ext;
                if ($media->type() == 'image') {
                    $div .= '<img alt="' . $media->id() . '" ' . $id . ' src="' . $path . '" >';
                } elseif ($media->type() == 'sound') {
                    $div .= '<audio ' . $id . ' controls src="' . $path . '" </audio>';
                } elseif ($media->type() == 'video') {
                    $source = '<source src="' . $path . '" type="video/' . $ext . '" ' . $id . '>';
                    $div .= '<video controls>' . $source . '</video>';
                } else {
                    $div .= '<a href="' . $path . '" target="_blank" class="media" ' . $id . '>' . $filename . '</a>';
                }
                if ($this->filename && in_array($media->type(), ['image', 'sound', 'video'])) {
                    $div .= "<div class=\"filename\">$filename</div>";
                }
                $div .= "</div>\n";
            }

            $div .= "</div>\n";

            return $div;
        }
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

    public function getpathadress(string $path): string
    {
        $query = ['path' => '/' . $path, 'sortby' => $this->sortby, 'order' => $this->order];
        if (array_diff(Media::mediatypes(), $this->type) != []) {
            $query['type'] = $this->type;
        }
        return '?' . urldecode(http_build_query($query));
    }

    public function getquery()
    {
        $query = [
            'path' => $this->path,
            'sortby' => $this->sortby,
            'order' => $this->order,
            'filename' => $this->filename
        ];
        if (array_diff(Media::mediatypes(), $this->type) !== []) {
            $query['type'] = $this->type;
        }
        return urldecode(http_build_query($query));
    }

    /**
     * Get the code to insert directly
     */
    public function getcode(): string
    {
        return '%MEDIA?' . $this->getquery() . '%';
    }

    public function getaddress(): string
    {
        return '?' . $this->getquery();
    }


    // ______________________________________________ G E T ________________________________________________________


    public function fullmatch()
    {
        return $this->fullmatch;
    }

    public function options()
    {
        return $this->options;
    }

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
        return ltrim($this->path, '/') . '/';
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

    public function filename(): int
    {
        return $this->filename;
    }

    // ______________________________________________ S E T ________________________________________________________


    public function setfullmatch(string $fullmatch)
    {
        $this->fullmatch = $fullmatch;
    }


    public function setoptions(string $options)
    {
        if (!empty($options)) {
            $this->options = $options;
        }
    }

    public function setpath(string $path)
    {
        // gather nested slashs
        $path = preg_replace("%\/{2,}%", "/", $path);
        if (preg_match('%^\/' . rtrim(Model::MEDIA_DIR, DIRECTORY_SEPARATOR) . '%', $path)) {
            $this->path = rtrim($path, DIRECTORY_SEPARATOR);
        } elseif (!preg_match('%^\/%', $path)) {
            $this->path = '/' . Model::MEDIA_DIR . rtrim($path, DIRECTORY_SEPARATOR);
        }
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

    public function setfilename($filename)
    {
        $this->filename = (int) (bool) $filename;
    }
}
