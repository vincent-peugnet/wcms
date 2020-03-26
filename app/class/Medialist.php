<?php

namespace Wcms;

class Medialist extends Item
{
    /** @var string full regex match */
    protected $fullmatch;

    /** @var string full filter code line */
    protected $filter = '';

    /** @var string directory of media */
    protected $path = '';

    /** @var string */
    protected $sortby = 'id';

    /** @var int */
    protected $order = 1;

    /** @var array list of media type to display */
    protected $type = [];

    /** @var int display media contents*/
    protected $display = 1;

    /** @var int display download links*/
    protected $links = 0;

    /** @var string display the file name of the file */
    protected $filename = 0;

    const TYPES = ['image', 'sound', 'video', 'other'];



    // __________________________________________________ F U N ____________________________________________________________



    public function __construct(array $datas = [])
    {
        $this->type = Model::mediatypes();
        $this->hydrate($datas);
    }

    public function readfilter()
    {
        parse_str($this->filter, $datas);
        $this->hydrate($datas);
    }

    public function generatecontent()
    {
        $mediamanager = new Modelmedia();
        $medialist = $mediamanager->getlistermedia($this->dir(), $this->type);
        if (!$medialist) {
            $this->content = '<strong>RENDERING ERROR :</strong> path : <code>' . Model::MEDIA_DIR . $this->path . '/</code> does not exist';
            return false;
        } else {

            $mediamanager->medialistsort($medialist, $this->sortby, $this->order);

            $dirid = str_replace('/', '-', $this->path);

            $div = '<div class="medialist" id="' . $dirid . '">' . PHP_EOL;

            foreach ($medialist as $media) {
                $div .= '<div class="content ' . $media->type() . '">';
                if ($media->type() == 'image') {
                    $div .= '<img alt="' . $media->id() . '" id="' . $media->id() . '" src="' . $media->getincludepath() . '" >';
                } elseif ($media->type() == 'sound') {
                    $div .= '<audio id="' . $media->id() . '" controls src="' . $media->getincludepath() . '" </audio>';
                } elseif ($media->type() == 'video') {
                    $div .= '<video controls><source src="' . $media->getincludepath() . '" type="video/' . $media->extension() . '"></video>';
                } elseif ($media->type() == 'other') {
                    $div .= '<a href="' . $media->getincludepath() . '" target="_blank" class="media" >' . $media->id() . '.' . $media->extension() . '</a>';
                }
                $div .= '</div>' . PHP_EOL;
            }

            $div .= '</div>' . PHP_EOL;

            return $div;
        }
    }

    /**
     * Generate link adress for table header
     * 
     * @param string $sortby 
     * @return string link adress
     */
    public function getsortbyadress(string $sortby) : string
    {
        if(!in_array($sortby, Model::MEDIA_SORTBY)) {
            $sortby = 'id';
        }
		if ($this->sortby === $sortby) {
			$order = $this->order * -1;
		} else {
			$order = $this->order;
		}
        $query = ['path' => $this->path, 'sortby' => $sortby, 'order' => $order];
        if(array_diff( self::TYPES, $this->type) != []) {
            $query['type'] = $this->type;
        }
        return '?' . urldecode(http_build_query($query));

    }

    public function getpathadress(string $path) : string
    {
        $query = ['path' => '/' . $path, 'sortby' => $this->sortby, 'order' => $this->order];
        if(array_diff( self::TYPES, $this->type) != []) {
            $query['type'] = $this->type;
        }
        return '?' . urldecode(http_build_query($query));
    }

    public function getquery()
    {
        $query = ['path' => $this->path, 'sortby' => $this->sortby, 'order' => $this->order];
        if(array_diff( self::TYPES, $this->type) != []) {
            $query['type'] = $this->type;
        }
        return '%MEDIA?' . urldecode(http_build_query($query)). '%';
    }


    // __________________________________________________ G E T ____________________________________________________________


    public function fullmatch()
    {
        return $this->fullmatch;
    }

    public function filter()
    {
        return $this->filter;
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

    // __________________________________________________ S E T ____________________________________________________________


    public function setfullmatch(string $fullmatch)
    {
        $this->fullmatch = $fullmatch;
    }


    public function setfilter(string $filter)
    {
        if (!empty($filter)) {
            $this->filter = $filter;
        }
    }

    public function setpath(string $path)
    {
        if(preg_match('%^\/' . rtrim(Model::MEDIA_DIR, DIRECTORY_SEPARATOR) . '%', $path)) {
            $this->path = rtrim($path, DIRECTORY_SEPARATOR);
        } elseif (!preg_match('%^\/%', $path)) {
            $this->path = '/' . Model::MEDIA_DIR . rtrim($path, DIRECTORY_SEPARATOR); 
        }
    }

    public function setsortby(string $sortby)
    {
        if (in_array($sortby, Model::MEDIA_SORTBY)) {
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
        if(is_array($type)) {
            $this->type = array_intersect(Model::mediatypes(), array_unique($type));
        }
    }
}
