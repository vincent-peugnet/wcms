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

        foreach ($matches as $match) {
            $element = new Elementv1($this->page->id(), $match->type());
            $element->hydrate($match->readoptions());
            $element->setcontent($this->getelementcontent($element->id(), $element->type()));
            $element->setcontent($this->elementparser($element));
            $body = str_replace($match->fullmatch(), $element->content(), $body);
        }

        return $body;
    }

    protected function elementparser(Elementv1 $element): string
    {
        $content = $element->content();
        $content = $this->winclusions($content);
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
     * @param string[] $sources             Array of pages ID
     * @param string $type                  Type of element
     */
    protected function getelementcontent(array $sources, string $type): string
    {
        if (!in_array($type, Pagev1::HTML_ELEMENTS)) {
            throw new DomainException("$type is not a valid HTML element type");
        }
        $content = '';
        $subseparator = "\n\n";
        foreach ($sources as $source) {
            if ($source !== $this->page->id()) {
                try {
                    $page = $this->pagemanager->get($source);
                    if ($page instanceof Pagev1) {
                        $subcontent = $page->$type();
                    } else {
                        $subcontent = $this->page->$type();
                    }
                } catch (RuntimeException $e) {
                    // Page ID is not used
                    $subcontent = $this->page->$type();
                }
            } else {
                $subcontent = $this->page->$type();
            }
            $content .= $subseparator . $subcontent;
        }
        return $content . $subseparator;
    }
}
