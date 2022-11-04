<?php

namespace Wcms;

class Mediaoptlist extends Mediaopt
{
    /** @var string full regex match */
    protected $fullmatch;

    /** @var string full options code line */
    protected $options = '';

    /** @var int display the file name of the file */
    protected int $filename = 0;

    public function __construct(array $datas = [])
    {
        parent::__construct($datas);
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
                $id = 'id="media_' . $media->filename() . '"';
                $path = $media->getincludepath();
                $ext = $media->extension();
                $filename = $media->filename() . '.' . $ext;
                if ($media->type() == 'image') {
                    $div .= '<img alt="' . $media->filename() . '" ' . $id . ' src="' . $path . '" >';
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

    public function setfilename($filename)
    {
        $this->filename = (int) (bool) $filename;
    }
}
