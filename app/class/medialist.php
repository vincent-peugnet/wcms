<?php

class Medialist
{
    /** @var string full regex match */
    protected $fullmatch;

    /** @var string options */
    protected $options = '';

    /** @var string directory of media */
    protected $path = '';

    /** @var string */
    protected $sortby = 'id';

    /** @var int */
    protected $order = 1;

    /** @var int display media contents*/
    protected $display = 1;

    /** @var int display download links*/
    protected $links = 0;

    /** @var string ouput html code generated*/
    protected $content = '';

    const SORT_BY_OPTIONS = ['id', 'size', 'type'];



    // __________________________________________________ F U N ____________________________________________________________



    public function __construct(array $datas = [])
    {
        $this->hydrate($datas);
        $this->readoptions();
        $this->generatecontent();
    }

    public function hydrate($datas)
    {
        foreach ($datas as $key => $value) {
            $method = 'set' . $key;

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function readoptions()
    {
        parse_str($this->options, $datas);
        $this->hydrate($datas);
    }

    public function generatecontent()
    {
        $mediamanager = new Modelmedia();
        $medialist = $mediamanager->getlistermedia(Model::MEDIA_DIR . $this->path . '/');
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

            $this->content = $div;

            return true;
        }
    }


    // __________________________________________________ G E T ____________________________________________________________


    public function fullmatch()
    {
        return $this->fullmatch;
    }

    public function options()
    {
        return $this->options;
    }

    public function content()
    {
        return $this->content;
    }



    // __________________________________________________ S E T ____________________________________________________________


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
        $this->path = $path;
    }

    public function setsortby(string $sortby)
    {
        if (in_array($sortby, self::SORT_BY_OPTIONS)) {
            $this->sortby = $sortby;
        }
    }

    public function setorder(int $order)
    {
        if ($order === -1 || $order === 1) {
            $this->order = $order;
        }
    }
}
