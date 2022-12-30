<?php

namespace Wcms;

use AltoRouter;
use DateTime;
use DOMDocument;
use DOMException;
use DOMText;
use LogicException;
use RuntimeException;
use Wcms\Exception\Database\Notfoundexception;
use Wcms\Notfoundexception as WcmsNotfoundexception;

class Servicerss
{
    protected AltoRouter $router;
    protected Servicerender $render;
    protected Modelpage $pagemanager;
    protected Modelbookmark $bookmarkmanager;

    public function __construct(AltoRouter $router)
    {
        $this->router = $router;
        $this->pagemanager = new Modelpage(Config::pagetable());
        $this->bookmarkmanager = new Modelbookmark();
        $this->render = new Servicerender($this->router, $this->pagemanager);
    }

    /**
     * @param Bookmark $bookmark
     *
     * @throws DOMException                 in case of XML render errors
     * @throws Filesystemexception          in case of file writing error
     * @throws RuntimeException             if Ref page is not found
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
     * @throws RuntimeException             if Ref page is not found
     */
    protected function render(array $pagelist, Bookmark $bookmark): string
    {
        $now = new DateTime();

        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $feed = $xml->createElement('feed');
        $feed->setAttribute("xmlns", "http://www.w3.org/2005/Atom");
        $feed->setAttribute('xml:lang', Config::lang());

        $title = $xml->createElement("title");
        $title->appendChild(new DOMText($bookmark->name()));
        $feed->appendChild($title);

        $id = $xml->createElement("id", Config::domain() . self::atompath($bookmark->id()));
        $feed->appendChild($id);

        if (!empty($bookmark->description())) {
            $subtitle = $xml->createElement("subtitle");
            $subtitle->appendChild(new DOMText($bookmark->description()));
            $feed->appendChild($subtitle);
        }

        $linkrss = $xml->createElement("link");
        $linkrss->setAttribute("href", Config::domain() . self::atompath($bookmark->id()));
        $linkrss->setAttribute("rel", "self");
        $feed->appendChild($linkrss);

        if (!empty($bookmark->ref())) {
            $ref = $this->pagemanager->get($bookmark->ref());
            $link = $xml->createElement("link");
            $link->setAttribute("href", $this->href($ref));
            $link->setAttribute("hreflang", !empty($ref->lang()) ? $ref->lang() : Config::lang());
            $link->setAttribute("rel", "alternate");
            if (!empty($ref->description())) {
                $link->setAttribute("title", $ref->description());
            }
            $feed->appendChild($link);
        }

        $generator = $xml->createElement("generator", "W-cms");
        $generator->setAttribute("uri", "https://w.club1.fr");
        $generator->setAttribute("version", getversion());
        $feed->appendChild($generator);

        if (!empty(Config::defaultfavicon())) {
            $icon = $xml->createElement("icon", Config::domain() . Model::faviconpath() . Config::defaultfavicon());
            $feed->appendChild($icon);
        }

        if (!empty(Config::defaultthumbnail())) {
            $logo = $xml->createElement("logo", Config::domain() . Model::thumbnailpath() . Config::defaultthumbnail());
            $feed->appendChild($logo);
        }


        $updated = $xml->createElement("updated", $now->format(DateTime::RFC3339));
        $feed->appendChild($updated);

        foreach ($pagelist as $page) {
            $entry = $xml->createElement("entry");
            if (!empty($page->lang())) {
                $entry->setAttribute('xml:lang', $page->lang());
            }
            $feed->appendChild($entry);

            $title = $xml->createElement("title");
            $title->appendChild(new DOMText($page->title()));
            $entry->appendChild($title);

            $id = $xml->createElement("id", $this->href($page));
            $entry->appendChild($id);

            $link = $xml->createElement("link");
            $link->setAttribute("href", $this->href($page));
            $link->setAttribute("hreflang", !empty($page->lang()) ? $page->lang() : Config::lang());
            $entry->appendChild($link);

            $published = $xml->createElement("published", $page->date()->format(DateTime::RFC3339));
            $entry->appendChild($published);

            $updated = $xml->createElement("updated", $page->date()->format(DateTime::RFC3339));
            $entry->appendChild($updated);

            $usermanager = new Modeluser();
            foreach ($page->authors() as $author) {
                try {
                    $user = $usermanager->get($author);
                    $author = $xml->createElement("author");
                    $name = $xml->createElement("name");
                    $name->appendChild(new DOMText(empty($user->name()) ? $user->id() : $user->name()));
                    $author->appendChild($name);
                    $entry->appendChild($author);
                } catch (Notfoundexception $e) {
                }
            }

            if (!empty($page->description())) {
                $summary = $xml->createElement("summary");
                $summary->appendChild(new DOMText($page->description()));
                $entry->appendChild($summary);
            }

            $content = $xml->createElement("content");
            $content->appendChild(
                new DOMText(html_entity_decode($this->mainhtml($page), ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8"))
            );
            $content->setAttribute("type", "html");
            $entry->appendChild($content);
        }
        $xml->appendChild($feed);
        return $xml->saveXML();
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
        $render = new Servicerender($this->router, $this->pagemanager);
        return $render->rendermain($page);
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
        } catch (WcmsNotfoundexception $e) {
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
        } catch (Folderexception $e) {
            Fs::dircheck(dirname($filename), true);
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

    /**
     * @return string                       Path to atome file including basepath
     */
    public static function atompath(string $id): string
    {
        return Model::dirtopath(self::atomfile($id));
    }
}
