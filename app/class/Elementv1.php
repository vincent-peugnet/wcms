<?php

namespace Wcms;

use DomainException;

class Elementv1 extends Element
{
    /** @var string[] */
    protected array $sources = [];
    protected string $type;

    /** @var bool Include element with HTML tags */
    protected bool $tag;


    public function __construct(string $pageid, string $fullmatch, string $type, string $options)
    {
        $this->tag = Config::htmltag();
        $this->urllinker = Config::urllinker();
        $this->fullmatch = $fullmatch;
                $type = strtolower($type);
        if (in_array($type, Pagev1::HTML_ELEMENTS)) {
            $this->type = $type;
        } else {
            throw new DomainException("$type is not a valid Page HTML Element Type");
        }
        $this->options = $options;
        $this->analyse($pageid);
    }

    protected function analyse(string $pageid): void
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

    /**
     * @return string[]
     */
    public function sources(): array
    {
        return $this->sources;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function tag(): bool
    {
        return $this->tag;
    }

    public function settag(bool $tag): void
    {
        $this->tag = $tag;
    }
}
