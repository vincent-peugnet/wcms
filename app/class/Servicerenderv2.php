<?php

namespace Wcms;

use DomainException;
use RuntimeException;

class Servicerenderv2 extends Servicerender
{
    /** @var Pagev2 $page */
    protected Page $page;

    /**
     * Render a full page V2 as HTML
     *
     * @param Page $page                    Page to render
     *
     * @return string                       HTML render of the page
     */
    public function render(Page $page): string
    {
        if (!$page instanceof Pagev2) {
            throw new DomainException('Page should be only Pagev2');
        }
        return parent::render($page);
    }

    public function renderprimary(Page $page): string
    {
        if (!$page instanceof Pagev2) {
            throw new DomainException('Page should be only Pagev2');
        }
        $this->page = $page;
        $html = $this->bodyconstructor('%CONTENT%');
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

        $matches = $this->match($body, 'CONTENT');

        // First, analyse the synthax and call the corresponding methods
        if (!empty($matches)) {
            foreach ($matches as $match) {
                $element = new Elementv2($this->page->id());
                $element->hydrate($match->readoptions());
                $element->setcontent($this->getelementcontent($element->id()));
                $element->setcontent($this->elementparser($element));
                $body = str_replace($match->fullmatch(), $element->content(), $body);
            }
        }

        return $body;
    }

    protected function elementparser(Elementv2 $element): string
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
            );
        }
        if ($element->urllinker()) {
            $content = $this->autourl($content);
        }

        return $content;
    }


    /**
     * Get element content by looking for source page ID.
     * If ID is not used: return empty string
     * If source page is V1, it will use the MAIN content.
     *
     * @param string $source                Source Page ID
     *
     * @return string                       Source Page primary content or empty string
     *
     * @todo Log errors somewhere
     */
    protected function getelementcontent(string $source): string
    {
        if ($source === $this->page->id()) {
            return $this->page->content();
        } else {
            try {
                $page = $this->pagemanager->get($source);
                return $page->primary();
            } catch (RuntimeException $e) {
                // page ID is not used
                return '';
            }
        }
    }
}
