<?php

namespace Wcms;

use AltoRouter;
use DateTime;
use DOMDocument;
use DOMException;
use LogicException;
use RuntimeException;
use Throwable;

class Servicerss
{
    protected AltoRouter $router;
    protected Modelrender $render;
    protected Modelpage $pagemanager;
    protected Modelbookmark $bookmarkmanager;

    public function __construct(AltoRouter $router)
    {
        $this->router = $router;
        $this->render = new Modelrender($this->router);
        $this->pagemanager = new Modelpage();
        $this->bookmarkmanager = new Modelbookmark();
    }

    /**
     * @param Bookmark $bookmark
     *
     * @throws DOMException                 in case of XML render errors
     * @throws Filesystemexception          in case of file writing error
     */
    public function publishbookmark(Bookmark $bookmark): void
    {
        $opt = $this->parsehydrate($bookmark->query());

        $pagelist = $this->pagemanager->pagelist();
        $pagetable = $this->pagemanager->pagetable($pagelist, $opt, '', []);

        $xml = $this->render($pagetable, $bookmark);
        $this->writeatom($bookmark->id(), $xml);
    }



    /**
     * Hydrate code into Object properties
     *
     * @param string $encoded               Encoded datas in code (can start with a `?` or not)
     *
     * @return Opt
     */
    protected function parsehydrate(string $encoded): Opt
    {
        parse_str(ltrim($encoded, "?"), $datas);
        return new Opt($datas);
    }

    /**
     * @param Page[] $pagelist              sorted and filtered list of page that will be in the RSS feed
     * @param Bookmark $bookmark            The actual page from which the RSS is linked
     *
     * @return string                       the RSS/Atom 1 as XML
     *
     * @throws DOMException                 if XML fail to build
     */
    protected function render(array $pagelist, Bookmark $bookmark): string
    {
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

        // link to reference page
        //
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
    protected function href(Page $page): string
    {
        return Config::domain() . $this->render->upage($page->id());
    }

    /**
     * Get the HTML output of a page
     *
     * @param Page $page
     * @return string                       HTML content parsed from page MAIN
     */
    protected function mainhtml(Page $page): string
    {
        $render = new Modelrender($this->render->router(), $this->render->pagelist());
        return $render->rsscontent($page);
    }

    /**
     * Remove Atom file if it exist
     *
     * @throws RuntimeException             If PHP unlink function fails
     */
    public static function removeatom(string $id): void
    {
        try {
            Fs::delete(self::atomfile($id));
        } catch (Notfoundexception $e) {
            // do nothing, this means file is already deleted
        } catch (Unlinkexception $e) {
            throw new RuntimeException("RSS atom file deletion error", 0, $e);
        }
    }

    /**
     * @param string $id                    Id of atom file
     * @param string $xml                   Atom file content
     *
     * @throws Filesystemexception
     */
    protected function writeatom(string $id, string $xml): void
    {
        $filename = self::atomfile($id);
        try {
            Fs::writefile($filename, $xml, 0664);
        } catch (Folderexception) {
            Fs::dircheck(dirname($filename));
            Fs::writefile($filename, $xml, 0664);
        }
    }

    /**
     * Get the atom File location
     *
     * @param string $id                    Bookmark Id
     * @return string                       Atom file location
     */
    public static function atomfile(string $id): string
    {
        return Model::ASSETS_ATOM_DIR . $id . '.xml';
    }

    public static function atompath(string $id): string
    {
        return Model::dirtopath(self::atomfile($id));
    }
}
