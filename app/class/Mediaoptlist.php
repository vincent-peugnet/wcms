<?php

namespace Wcms;

use RuntimeException;

class Mediaoptlist extends Mediaopt
{
    /** @var string full regex match */
    protected $fullmatch;

    /** @var string full options code line */
    protected $options = '';

    /** @var bool display the file name of the file */
    protected bool $filename = false;

    /**
     * @param object|array $datas
     */
    public function __construct($datas = [])
    {
        parent::__construct($datas);
    }

    public function readoptions(): void
    {
        parse_str(htmlspecialchars_decode($this->options), $datas);
        $this->hydrate($datas);
    }

    /**
     * Generate HTML displaying list of medias
     *
     * @throws RuntimeException             If something went wrong
     */
    public function generatecontent(): string
    {
        $mediamanager = new Modelmedia();
        $medialist = $mediamanager->medialistopt($this);

        $dirid = str_replace('/', '-', $this->path);

        $div = "<div class=\"medialist\" id=\"$dirid\">\n";

        foreach ($medialist as $media) {
            $div .= '<div class="content ' . $media->type() . '">';
            $id = 'id="media_' . $media->filename() . '"';
            $path = $media->getincludepath();
            $ext = $media->extension();
            $filename = $media->filename();
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

    public function getquery(): string
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


    public function fullmatch(): string
    {
        return $this->fullmatch;
    }

    public function options(): string
    {
        return $this->options;
    }

    public function filename(): bool
    {
        return $this->filename;
    }

    // ______________________________________________ S E T ________________________________________________________


    public function setfullmatch(string $fullmatch): void
    {
        $this->fullmatch = $fullmatch;
    }


    public function setoptions(string $options): void
    {
        if (!empty($options)) {
            $this->options = $options;
        }
    }

    public function setfilename(bool $filename): void
    {
        $this->filename = $filename;
    }
}
