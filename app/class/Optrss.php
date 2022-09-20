<?php

namespace Wcms;

use DateTime;
use DOMDocument;
use DOMException;
use LogicException;

class Optrss extends Opt
{
    /** @var Modelrender $render */
    protected $render;

    /**
     * Hydrate code into Object properties
     *
     * @param string $encoded Encoded datas in code (can start with a `?` or not)
     *
     * @return bool indicating the success of hydrating protocol
     */
    public function parsehydrate(string $encoded): bool
    {
        parse_str(ltrim($encoded, "?"), $datas);
        return $this->hydrate($datas);
    }

    /**
     * @param Page[] $pagelist              sorted and filtered list of page that will be in the RSS feed
     * @param Bookmark $bookmark            The actual page from which the RSS is linked
     * @param Modelrender $render           the Rendering engine to generate links
     *
     * @return string                       the RSS/Atom 1 as XML
     *
     * @throws DOMException                 if XML fail to build
     */
    public function render(array $pagelist, Bookmark $bookmark, Modelrender $render): string
    {
        $this->render = $render;
        $now = new DateTime();

        $xml = new DOMDocument('1.0', 'utf-8');
        $xml->formatOutput = true;

        $feed = $xml->createElement('feed');
        $feed->setAttribute("xmlns", "http://www.w3.org/2005/Atom");

        $title = $xml->createElement("title", $bookmark->name());
        $feed->appendChild($title);

        $subtitle = $xml->createElement("subtitle", $bookmark->description());
        $feed->appendChild($subtitle);

        $linkrss = $xml->createElement("link");
        $linkrss->setAttribute("href", Config::domain() . Model::renderpath() . $bookmark->id() . '.xml');
        $linkrss->setAttribute("rel", "self");
        $feed->appendChild($linkrss);

        // $link = $xml->createElement("link");
        // $link->setAttribute("href", $this->href($page));
        // $link->setAttribute("hreflang", !empty($page->lang()) ? $page->lang() : Config::lang());
        // $feed->appendChild($link);

        $updated = $xml->createElement("updated", $now->format(DateTime::ISO8601));
        $feed->appendChild($updated);

        foreach ($pagelist as $page) {
            $entry = $xml->createElement("entry");
            $feed->appendChild($entry);

            $title = $xml->createElement("title", $page->title());
            $entry->appendChild($title);

            $link = $xml->createElement("link");
            $link->setAttribute("href", $this->href($page));
            $link->setAttribute("hreflang", !empty($page->lang()) ? $page->lang() : Config::lang());
            $entry->appendChild($link);

            $published = $xml->createElement("published", $page->date('string'));
            $entry->appendChild($published);

            $updated = $xml->createElement("updated", $page->daterender('string'));
            $entry->appendChild($updated);

            $summary = $xml->createElement("summary", $page->description());
            $entry->appendChild($summary);

            $content = $xml->createElement("content", $this->mainhtml($page));
            $content->setAttribute("type", "hmtl");
            $entry->appendChild($content);
        }
        return $xml->saveXML($feed);
    }

    /**
     * Generate links for XML, Modelrender has to be already loaded as a property
     *
     * @param Page $page                    page quoted for the link
     *
     * @return string
     *
     * @throws LogicException               if router fail to generate route
     */
    public function href(Page $page): string
    {
        return Config::domain() . $this->render->upage($page->id());
    }

    /**
     * Get the HTML output of a page
     *
     * @param Page $page
     * @return string                       HTML content parsed from page MAIN
     */
    public function mainhtml(Page $page): string
    {
        $render = new Modelrender($this->render->router(), $this->render->pagelist());
        return $render->rsscontent($page);
    }
}
