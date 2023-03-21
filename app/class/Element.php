<?php

namespace Wcms;

use DomainException;

/**
 * HTML Element used in pages
 */
class Element extends Item
{
    protected $fullmatch;
    protected string $type;
    protected $options;
    protected $sources = [];
    protected int $everylink = 0;
    protected bool $markdown = true;
    protected string $content = '';
    protected int $minheaderid = 1;
    protected int $maxheaderid = 6;
    protected $headerid = '1-6';
    /** @var bool default value is set in Config class */
    protected bool $urllinker;
    protected int $headeranchor = self::NOHEADERANCHOR;

    /** @var bool Include element with HTML tags */
    protected bool $tag;

    public const NOHEADERANCHOR = 0;
    public const HEADERANCHORLINK = 1;
    public const HEADERANCHORHASH = 2;
    public const HEADERANCHORMODES = [
        self::NOHEADERANCHOR, self::HEADERANCHORLINK, self::HEADERANCHORHASH
    ];

    // ______________________________________________ F U N ________________________________________________________



    public function __construct($pageid, $datas = [])
    {
        $this->urllinker = Config::urllinker();
        $this->tag = Config::htmltag();
        $this->hydrate($datas);
        $this->analyse($pageid);
    }

    private function analyse(string $pageid)
    {
        if (!empty($this->options)) {
            $this->options = str_replace('*', $pageid, $this->options);
            parse_str($this->options, $datas);
            if (isset($datas['id'])) {
                $this->sources = explode(' ', $datas['id']);
            } else {
                $this->sources = [$pageid];
            }
            $this->hydrate($datas);
        } else {
            $this->sources = [$pageid];
        }
    }





    // ______________________________________________ G E T ________________________________________________________


    public function fullmatch(): string
    {
        return $this->fullmatch;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function options(): string
    {
        return $this->options;
    }

    public function sources(): array
    {
        return $this->sources;
    }

    public function everylink(): int
    {
        return $this->everylink;
    }

    public function markdown(): bool
    {
        return $this->markdown;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function minheaderid(): int
    {
        return $this->minheaderid;
    }

    public function maxheaderid(): int
    {
        return $this->maxheaderid;
    }

    public function headerid(): string
    {
        return $this->headerid;
    }

    public function headeranchor(): int
    {
        return $this->headeranchor;
    }

    public function urllinker(): bool
    {
        return $this->urllinker;
    }

    public function tag(): bool
    {
        return $this->tag;
    }






    // ______________________________________________ S E T ________________________________________________________


    public function setfullmatch(string $fullmatch)
    {
        $this->fullmatch = $fullmatch;
    }

    /**
     * @throws DomainException if given type is not an HTML element
     */
    public function settype(string $type)
    {
        $type = strtolower($type);
        if (in_array($type, Model::HTML_ELEMENTS)) {
            $this->type = $type;
        } else {
            throw new DomainException("$type is not a valid Page HTML Element Type");
        }
    }

    public function setoptions(string $options)
    {
        if (!empty($options)) {
            $this->options = $options;
        }
    }

    public function seteverylink(int $level)
    {
        if ($level >= 0 && $level <= 16) {
            $this->everylink = $level;
            return true;
        } else {
            return false;
        }
    }

    public function setmarkdown($markdown)
    {
        $this->markdown = boolval($markdown);
    }

    public function setcontent(string $content)
    {
        $this->content = $content;
    }

    public function setheaderid(string $headerid)
    {
        if ($headerid == 0) {
            $this->headerid = 0;
        } else {
            preg_match('~([1-6])\-([1-6])~', $headerid, $out);
            $this->minheaderid = intval($out[1]);
            $this->maxheaderid = intval($out[2]);
        }
    }

    public function setheaderanchor($headeranchor)
    {
        if (in_array($headeranchor, self::HEADERANCHORMODES)) {
            $this->headeranchor = (int) $headeranchor;
        }
    }

    public function seturllinker($urllinker)
    {
        $this->urllinker = boolval($urllinker);
    }

    public function settag($tag)
    {
        $this->tag = boolval($tag);
    }
}
