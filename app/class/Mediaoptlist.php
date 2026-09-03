<?php

namespace Wcms;

use DOMDocument;
use DOMException;
use LogicException;
use RuntimeException;

class Mediaoptlist extends Mediaopt
{
    /** @var bool display the file name of the file */
    protected bool $filename = false;

    /**
     * @param object|array<string, mixed> $datas
     */
    public function __construct($datas = [])
    {
        parent::__construct($datas);
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

        try {
            $dom = new DOMDocument('1.0', 'UTF-8');

            $ul = $dom->createElement('ul');
            $ul->setAttribute('class', 'medialist');

            foreach ($medialist as $media) {
                $li = $dom->createElement('li');
                $li->setAttribute('data-type', $media->type());

                // `internal` or/and `media` classes are added during HTML parsing

                switch ($media->type()) {
                    case Media::IMAGE:
                        $link = false;
                        $m = $dom->createElement('img');
                        $m->setAttribute('src', $media->getincludepath());
                        break;

                    case Media::SOUND:
                        $link = false;
                        $m = $dom->createElement('audio');
                        $m->setAttribute('controls', '');
                        $m->setAttribute('src', $media->getincludepath());
                        break;

                    case Media::VIDEO:
                        $link = false;
                        $m = $dom->createElement('video');
                        $m->setAttribute('controls', '');
                        $m->setAttribute('src', $media->getincludepath());
                        break;

                    default:
                        $link = true;
                        $m = $dom->createElement('a', $media->filename());
                        $m->setAttribute('href', $media->getincludepath());
                        $m->setAttribute('target', '_blank');
                        break;
                }

                $li->appendChild($m);

                if ($this->filename && !$link) {
                    $p = $dom->createElement('p', $media->filename());
                    $li->appendChild($p);
                }

                $ul->appendChild($li);
            }

            $dom->appendChild($ul);

            return $dom->saveHTML($dom->documentElement);
        } catch (DOMException $e) {
            throw new LogicException('bad DOM node used', 0, $e);
        }
    }

    public function getquery(): string
    {
        $query = [
            'path' => $this->path,
            'sortby' => $this->sortby,
            'order' => $this->order,
            'filename' => $this->filename
        ];
        if (array_diff(Media::MEDIA_TYPES, $this->type) !== []) {
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


    public function filename(): bool
    {
        return $this->filename;
    }

    // ______________________________________________ S E T ________________________________________________________


    public function setfilename(bool $filename): void
    {
        $this->filename = $filename;
    }
}
