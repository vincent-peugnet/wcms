<?php

namespace Wcms;

use DomainException;
use RuntimeException;

class Servicerenderv1 extends Servicerender
{
    /** @var Pagev1 $page */
    protected Page $page;


    /**
     * Render a full page V1 as HTML
     *
     * @param Page $page                    Page to render
     *
     * @return string                       HTML render of the page
     */
    public function render(Page $page): string
    {
        if (!$page instanceof Pagev1) {
            throw new DomainException('Page should be only Pagev1');
        }
        return parent::render($page);
    }

    public function renderprimary(Page $page): string
    {
        if (!$page instanceof Pagev1) {
            throw new DomainException('Page should be only Pagev1');
        }
        $this->page = $page;
        $html = $this->bodyconstructor('%MAIN%');
        return $this->bodyparser($html);
    }

    /**
     * Analyse BODY, call the corresponding CONTENTs and render everything
     *
     * @param string $body as the string BODY of the page
     *
     * @return string as the full rendered BODY of the page
     */
    protected function bodyconstructor(string $body): string
    {
        $body = parent::bodyconstructor($body);

        // Elements that can be detected
        $types = array_map("strtoupper", Pagev1::HTML_ELEMENTS);
        $regex = implode("|", $types);
        $matches = $this->match($body, $regex);
        $replacements = [];

        if (empty($matches)) {
            return $body;
        }

        foreach ($matches as $match) {
            try {
                $element = new Elementv1($this->page->id(), $match->type());
                $element->hydrate($match->readoptions());
                $replacements[$match->fullmatch()] = $this->elementparser(
                    $element,
                    $this->getelementcontent($element)
                );
            } catch (RuntimeException $e) {
                $this->adderror("element inclusion: '%s': %s", $match->fullmatch(), $e->getMessage());
            }
        }

        return strtr($body, $replacements);
    }

    protected function elementparser(Elementv1 $element, string $content): string
    {
        $content = $this->winclusions($content);
        $content = $this->wikiurl($content);
        if ($element->everylink() > 0) {
            $content = $this->everylink($content, $element->everylink());
        }
        if ($element->markdown()) {
            $content = $this->markdown($content);
        }
        if ($element->headerid()) {
            $content = $this->headerid(
                $content,
                $element->minheaderid(),
                $element->maxheaderid(),
                $element->headeranchor(),
                $element->type()
            );
        }
        if ($element->urllinker()) {
            $content = $this->autourl($content);
        }
        if ($element->tag()) {
            $type = $element->type();
            $content = "\n<{$type}>\n{$content}\n</{$type}>\n";
        }

        return $content;
    }


    /**
     * Foreach $sources (pages), this will get the corresponding $type element content
     * If ID is not used or if Page is not version 1: fallback to current page Markdown field
     *
     * @param Elementv1 $element            Element
     *
     * @throws RuntimeException             If page is not compatible or not found
     */
    protected function getelementcontent(Elementv1 $element): string
    {
        $type = $element->type();
        if (!in_array($type, Pagev1::HTML_ELEMENTS)) {
            throw new DomainException("'$type' is not a valid page V1 element type");
        }
        $content = '';
        $subseparator = "\n\n";
        foreach ($element->id() as $source) {
            if ($source !== $this->page->id()) {
                $page = $this->pagemanager->get($source);
                if (! ($page instanceof Pagev1)) {
                    throw new RuntimeException(sprintf("not compatible: page '%s' is not page V1", $page->id()));
                }
                $subcontent = $page->$type();
            } else {
                $subcontent = $this->page->$type();
            }
            $content .= $subseparator . $subcontent;
        }
        return $content . $subseparator;
    }
}
