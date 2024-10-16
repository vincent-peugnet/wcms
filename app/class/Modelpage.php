<?php

namespace Wcms;

use AltoRouter;
use JamesMoss\Flywheel\Document;
use DateTimeImmutable;
use DateTimeInterface;
use DomainException;
use InvalidArgumentException;
use RangeException;
use RuntimeException;
use Wcms\Exception\Filesystemexception;
use Wcms\Exception\Filesystemexception\Notfoundexception;

class Modelpage extends Modeldb
{
    public const SECURE_LEVELS = [
        0 => 'public',
        1 => 'private',
        2 => 'not_published'
    ];

    /** @var Page[] $pagelist */
    protected array $pagelist = [];

    public function __construct(string $pagetable, string $pagedir = self::PAGES_DIR)
    {
        $this->dbinit($pagedir);
        $this->storeinit($pagetable);
    }

    /**
     * Scan library for all pages as objects.
     * If a scan has already been perform, it will just
     * read `pagelist` Propriety
     *
     * @return Page[]                       of Pages objects as `id => Page`
     */
    public function pagelist(): array
    {
        if (empty($this->pagelist)) {
            $list = $this->repo->findAll();
            foreach ($list as $pagedata) {
                $id = $pagedata->id;
                try {
                    $this->pagelist[$id] = $this->parsepage($pagedata);
                } catch (RuntimeException $e) {
                    Logger::error("Could not load Page with ID \"$id\" : $e");
                }
            }
        }
        return $this->pagelist;
    }


    /**
     * Scan database for specific pages IDs and return array of Pages objects
     *
     * @param string[] $idlist                 list of ID strings
     *
     * @return Page[]                           array of Page objects
     */
    public function pagelistbyid(array $idlist = []): array
    {
        $pagedatalist = $this->repo->query()
            ->where('__id', 'IN', $idlist)
            ->execute();

        $pagelist = [];
        foreach ($pagedatalist as $id => $pagedata) {
            try {
                $pagelist[$id] = $this->parsepage($pagedata);
            } catch (RuntimeException $e) {
                Logger::error("Could not load Page with ID \"$id\" : $e");
            }
        }
        return $pagelist;
    }

    /**
     * Store new page in the database
     *
     * @param Page $page                    Page object
     * @return bool                         depending on database storing
     */
    public function add(Page $page): bool
    {

        $pagedata = new Document($page->dry());
        $pagedata->setId($page->id());
        return $this->storedoc($pagedata);
    }

    /**
     * Obtain a page object from the database
     *
     * @param Page|string $id               could be an Page object or a id string
     *
     * @return Page                         The Page object
     *
     * @throws InvalidArgumentException     If $id argument is not a string or a Page
     * @throws RuntimeException             If page is'nt found
     */
    public function get($id): Page
    {
        if ($id instanceof Page) {
            $id = $id->id();
        }
        if (is_int($id)) {
            $id = strval($id);
        }
        if (is_string($id)) {
            $pagedata = $this->repo->findById($id);
            if ($pagedata === false) {
                throw new RuntimeException("Could not find Page with the following ID: \"$id\"");
            }
            try {
                return $this->parsepage($pagedata);
            } catch (RuntimeException $e) {
                throw new RuntimeException("Could not load Page with ID \"$id\": $e");
            }
        } else {
            throw new InvalidArgumentException("argument of Modelpage->get() should be a ID string or Page");
        }
    }

    /**
     * Check if a page exist or not
     *
     * @param Page|string $id               Could be an Page object or a id string
     *
     * @return bool                         True if page exists otherwise False
     */
    public function exist($id): bool
    {
        try {
            $this->get($id);
            return true;
        } catch (RuntimeException $e) {
            return false;
        }
    }

    /**
     * Transform File to Page Oject
     *
     * @return false|Page
     */
    public function getfromfile()
    {
        if (!isset($_FILES['pagefile']) || $_FILES['pagefile']['error'] > 0) {
            return false;
        }

        $ext = mb_substr(strrchr($_FILES['pagefile']['name'], '.'), 1);
        if ($ext !== 'json') {
            return false;
        }

        $json = file_get_contents($_FILES['pagefile']['tmp_name']);
        $pagedata = json_decode($json, true);

        if ($pagedata === false) {
            return false;
        }

        try {
            return $this->parsepage($pagedata);
        } catch (RuntimeException $e) {
            return false;
        }
    }

    /**
     * Get all the pages that are called in css templating
     *
     * @param Page $page                    page to retrieve css templates
     * @return Page[]                       array of pages with ID as index
     *
     * @throws RuntimeException             If css template is not found in database
     */
    public function getpagecsstemplates(Page $page): array
    {
        $templates = [];
        if (!empty($page->templatecss()) && $page->templatecss() !== $page->id()) {
            $template = $this->get($page->templatecss());
            $templates[$template->id()] = $template;
            $templates = array_merge($this->getpagecsstemplates($template), $templates);
        }
        return $templates;
    }

    /**
     * Get all the pages that are called in Javascript templating
     *
     * @param Page $page                    page to retrieve JS templates
     * @return Page[]                       array of pages with ID as index
     *
     * @throws RuntimeException             If JS template is not found in database
     */
    public function getpagejavascripttemplates(Page $page): array
    {
        $templates = [];
        if (!empty($page->templatejavascript()) && $page->templatejavascript() !== $page->id()) {
            $template = $this->get($page->templatejavascript());
            $templates[$template->id()] = $template;
            $templates = array_merge($this->getpagejavascripttemplates($template), $templates);
        }
        return $templates;
    }

    /**
     * Get Page's favicon filename using the priority order:
     * Page > BODY Template > Default
     *
     * @param Page $page
     * @return string                       Page's thumbnail file, or an empty string
     */
    public function getpagefavicon(Page $page): string
    {
        if (!empty($page->favicon())) {
            return $page->favicon();
        }
        if (!empty($page->templatebody())) {
            try {
                $templatebody = $this->get($page->templatebody());
                if (!empty($templatebody->favicon())) {
                    return $templatebody->favicon();
                }
            } catch (RuntimeException $e) {
                // Page BODY template does not exist
            }
        }
        if (!empty(Config::defaultfavicon())) {
            return Config::defaultfavicon();
        }
        return '';
    }

    /**
     * Get Page's thumbnail filename using the priority order:
     * Page > BODY Template > Default
     *
     * @param Page $page
     * @return string                       Thumbnail filename or empty string
     */
    public function getpagethumbnail(Page $page): string
    {
        if (!empty($page->thumbnail())) {
            return $page->thumbnail();
        }
        if (!empty($page->templatebody())) {
            try {
                $templatebody = $this->get($page->templatebody());
                if (!empty($templatebody->thumbnail())) {
                    return $templatebody->thumbnail();
                }
            } catch (RuntimeException $e) {
                // Page BODY template does not exist
            }
        }
        if (!empty(Config::defaultthumbnail())) {
            return Config::defaultthumbnail();
        }
        return '';
    }

    /**
     * Delete a page and it's linked rendered html and css files
     *
     * @param Page $page                    Page to delete
     *
     * @return bool                         true if success otherwise false
     *
     * @todo use Exception istead of returning boolean
     */
    public function delete(Page $page): bool
    {
        try {
            $this->unlink($page->id());
        } catch (Filesystemexception $e) {
            return false;
        }
        return $this->repo->delete($page->id());
    }

    /**
     * Delete rendered CSS, JS and HTML files associated with given Page
     *
     * @param string $pageid
     *
     * @throws Filesystemexception          If a file deletion failure occurs
     */
    public function unlink(string $pageid): void
    {
        $files = ['.css', '.quick.css', '.js'];
        foreach ($files as $file) {
            try {
                Fs::deletefile(Model::ASSETS_RENDER_DIR . $pageid . $file);
            } catch (Notfoundexception $e) {
                // do nothing, this means file is already deleted
            }
        }
        try {
            Fs::deletefile(Model::HTML_RENDER_DIR . $pageid . '.html');
        } catch (Notfoundexception $e) {
            // do nothing, this means file is already deleted
        }
    }

    /**
     * Empty RENDER_DIR and HTML_RENDER_DIR
     *
     * @throws RuntimeException in cas of falilure
     */
    public function flushrendercache(): void
    {
        try {
            Fs::folderflush(self::ASSETS_RENDER_DIR);
            Fs::folderflush(self::HTML_RENDER_DIR);
        } catch (Filesystemexception $e) {
            $fserror = $e->getMessage();
            throw new RuntimeException("Error while trying to flush page render cache: $fserror", 1);
        }
    }

    /**
     * Update a page in the database
     *
     * @todo Use Exceptions instead of returning bool
     *
     * @param Page $page                    The page that is going to be updated
     *
     * @return bool                         True if success otherwise, false
     *
     */
    public function update(Page $page)
    {
        $pagedata = new Document($page->dry());
        $pagedata->setId($page->id());
        return $this->updatedoc($pagedata);
    }

    /**
     * Edit a page based on meta infos
     *
     * @param string $pageid
     * @param mixed[] $datas
     * @param string[] $reset
     * @param string $addtag
     * @param string $addauthor
     *
     * @throws RuntimeException             When page is not found in the database or update failed
     */
    public function pageedit(string $pageid, array $datas, array $reset, string $addtag, string $addauthor): void
    {
        $page = $this->get($pageid);
        $page = $this->reset($page, $reset);
        $page->hydrate($datas);
        $page->addtag($addtag);
        $page->addauthor($addauthor);
        if (!$this->update($page)) {
            throw new RuntimeException("Error while trying to update page $pageid");
        }
    }

    /**
     * Reset values of a page
     *
     * @param Page $page                    Page object to be reseted
     * @param string[] $reset                  List of parameters needing reset
     *
     * @return Page                         The reseted page object
     */
    protected function reset(Page $page, array $reset): Page
    {
        $now = new DateTimeImmutable("now", timezone_open("Europe/Paris"));
        if (boolval($reset['tag'])) {
            $page->settag([]);
        }
        if (boolval($reset['geo'])) {
            $page->setlatitude(null);
            $page->setlongitude(null);
        }
        if (boolval($reset['author'])) {
            $page->setauthors([]);
        }
        if (boolval($reset['redirection'])) {
            $page->setredirection('');
        }
        if (boolval($reset['date'])) {
            $page->setdate($now);
        }
        if (boolval($reset['datemodif'])) {
            $page->setdatemodif($now);
        }
        return $page;
    }

    /**
     * Check if a page need to be rendered
     *
     * This will compare edit and render dates,
     * then if render file exists,
     * then if the templatebody is set and has been updated.
     *
     * @param Page $page                    Page to be checked
     *
     * @return bool                         true if the page need to be rendered otherwise false
     */
    public function needtoberendered(Page $page): bool
    {
        if (
            $page->daterender() <= $page->datemodif() ||
            !file_exists(self::HTML_RENDER_DIR . $page->id() . '.html') ||
            !file_exists(self::ASSETS_RENDER_DIR . $page->id() . '.css') ||
            !file_exists(self::ASSETS_RENDER_DIR . $page->id() . '.js')
        ) {
            return true;
        } elseif (!empty($page->templatebody())) {
            try {
                $bodytemplate = $this->get($page->templatebody());
                return $page->daterender() <= $bodytemplate->datemodif();
            } catch (RuntimeException $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Render given page
     * Write HTML, CSS and JS files
     * update linkto property
     *
     * @param Page $page input
     *
     * @return Page rendered $page
     *
     * @throws Runtimeexception if whriting files to filesystem failed
     */
    public function renderpage(Page $page, AltoRouter $router): Page
    {
        $now = new DateTimeImmutable("now", timezone_open("Europe/Paris"));

        $params = [$router, $this, Config::externallinkblank(), Config::internallinkblank()];

        switch ($page->version()) {
            case Page::V1:
                $renderengine = new Servicerenderv1(...$params);
                break;
            case Page::V2:
                $renderengine = new Servicerenderv2(...$params);
                break;
            default:
                throw new DomainException('Page version is out of range');
        }

        $html = $renderengine->render($page);
        Fs::dircheck(Model::ASSETS_RENDER_DIR, true, 0775);
        Fs::dircheck(Model::HTML_RENDER_DIR, true, 0775);
        Fs::writefile(Model::HTML_RENDER_DIR . $page->id() . '.html', $html);
        Fs::writefile(Model::ASSETS_RENDER_DIR . $page->id() . '.css', $page->css(), 0664);
        Fs::writefile(Model::ASSETS_RENDER_DIR . $page->id() . '.js', $page->javascript(), 0664);

        $page->setdaterender($now);
        $page->setlinkto($renderengine->linkto());
        $page->setpostprocessaction($renderengine->postprocessaction());

        return $page;
    }








    // _____________________________ FILTERING & SORTING _____________________________


    /**
     * Main page list filtering and sorting tool
     *
     * @param Page[] $pagelist             of Pages objects as `id => Page`
     * @param Opt $opt
     *
     * @param string $regex                 Regex to match.
     * @param string[] $searchopt              Option search, could be `content` `title` `description`.
     *
     * @return Page[]                       associative array of `Page` objects
     */
    public function pagetable(array $pagelist, Opt $opt, $regex = '', $searchopt = []): array
    {
        $pagelist = $this->filter($pagelist, $opt);
        if (!empty($regex)) {
            $pagelist = $this->deepsearch($pagelist, $regex, $searchopt);
        }
        $pagelist = $this->sort($pagelist, $opt);

        return $pagelist;
    }



    /**
     * Filter the pages list acording to the options and invert
     *
     * @param Page[] $pagelist              list of `Page` objects
     * @param Opt $opt
     *
     * @return Page[]                       Filtered list of pages
     */

    protected function filter(array $pagelist, Opt $opt): array
    {
        $filter = [];
        foreach ($pagelist as $page) {
            if (
                $this->ftag($page, $opt->tagfilter(), $opt->tagcompare(), $opt->tagnot()) &&
                $this->fauthor($page, $opt->authorfilter(), $opt->authorcompare()) &&
                $this->fsecure($page, $opt->secure()) &&
                $this->flinkto($page, $opt->linkto()) &&
                $this->fsince($page, $opt->since()) &&
                $this->funtil($page, $opt->until()) &&
                $this->fgeo($page, $opt->geo()) &&
                $this->fversion($page, $opt->version())
            ) {
                $filter[] = $page->id();
            }
        }

        if ($opt->invert()) {
            $idlist = array_keys($pagelist);
            $filter = array_diff($idlist, $filter);
        }

        return array_intersect_key($pagelist, array_flip($filter));
    }



    /**
     * Sort and limit an array of Pages
     *
     * @param Page[] $pagelist              array of `Page` objects
     * @param Opt $opt
     *
     * @return Page[]                       associative array of `Page` objects
     */
    protected function sort(array $pagelist, Opt $opt): array
    {
        $this->pagelistsort($pagelist, $opt->sortby(), $opt->order());

        if ($opt->limit() !== 0) {
            $pagelist = array_slice($pagelist, 0, $opt->limit(), true);
        }

        return $pagelist;
    }

    /**
     * @todo It can go in great-parent class Model to be used as well by Modelmedia. Using Item instead of Page
     */
    protected function pagecompare(Page $page1, Page $page2, $method = 'id', $order = 1): int
    {
        $result = ($page1->$method('sort') <=> $page2->$method('sort'));
        return $result * $order;
    }

    protected function buildsorter(string $sortby, int $order): callable
    {
        return function ($page1, $page2) use ($sortby, $order) {
            $result = $this->pagecompare($page1, $page2, $sortby, $order);
            return $result;
        };
    }


    /**
     * @param Page[] $pagelist
     *
     * @todo remove this method and put the content inside sort() method
     */
    protected function pagelistsort(array &$pagelist, string $sortby, $order = 1): bool
    {
        return uasort($pagelist, $this->buildsorter($sortby, $order));
    }


    /**
     * @param Page[] $pagelist              List of Page
     * @param string[] $tagchecked          List of tags
     * @param string $tagcompare            Can be 'OR' or 'AND', set the tag filter method
     *
     * @return string[]                     array of `string` page id
     */
    protected function filtertagfilter(array $pagelist, array $tagchecked, $tagcompare = 'OR'): array
    {

        $filteredlist = [];
        foreach ($pagelist as $page) {
            if ($this->ftag($page, $tagchecked, $tagcompare)) {
                $filteredlist[] = $page->id();
            }
        }
        return $filteredlist;
    }



    /**
     * @param Page $page                    Page
     * @param string[] $tagchecked          list of tags
     * @param string $tagcompare            can be 'OR' or 'AND', set the tag filter method
     *
     * @return bool                         true if Page pass test, otherwise false
     */
    protected function ftag(Page $page, array $tagchecked, string $tagcompare = Opt::OR, bool $tagnot = false): bool
    {
        if ($tagcompare === Opt::EMPTY) {
            return $tagnot xor empty($page->tag());
        }
        if (empty($tagchecked)) {
            return true;
        } else {
            if ($tagcompare === Opt::OR) {
                $inter = (array_intersect($page->tag('array'), $tagchecked));
                return $tagnot xor !empty($inter);
            } elseif ($tagcompare === Opt::AND) {
                $diff = !array_diff($tagchecked, $page->tag('array'));
                return $tagnot xor !empty($diff);
            }
            return false;
        }
    }


    /**
     * @param Page $page                    Page
     * @param string[] $authorchecked       List of authors
     * @param string $authorcompare         Cab be 'OR' or 'AND', set the author filter method
     *
     * @return bool                         true if Page pass test, otherwise false
     */
    protected function fauthor(Page $page, array $authorchecked, string $authorcompare = Opt::OR): bool
    {
        if ($authorcompare === Opt::EMPTY) {
            return empty($page->authors());
        }
        if (empty($authorchecked)) {
            return true;
        } else {
            if ($authorcompare === Opt::OR) {
                $inter = (array_intersect($page->authors('array'), $authorchecked));
                return (!empty($inter));
            } elseif ($authorcompare === Opt::AND) {
                return (!array_diff($authorchecked, $page->authors('array')));
            }
            return false;
        }
    }


    /**
     * @param Page $page                    Page
     * @param int $secure                   Secure level
     *
     * @return bool                         true if Page pass test, otherwise false
     */
    protected function fsecure(Page $page, int $secure): bool
    {
        if ($page->secure() === intval($secure)) {
            return true;
        } elseif (intval($secure) >= 4) {
            return true;
        }
        return false;
    }


    /**
     * @param Page $page                    Page
     * @param string $linkto                Page id used as linkto
     *
     * @return bool                         true if Page pass test, otherwise false
     */
    protected function flinkto(Page $page, string $linkto): bool
    {
        return (empty($linkto) || in_array($linkto, $page->linkto('array')));
    }


    /**
     * @param Page $page                    Page
     * @param ?DateTimeInterface $since     Minimum date for validatation
     *
     * @return bool
     */
    protected function fsince(Page $page, ?DateTimeInterface $since): bool
    {
        if (is_null($since)) {
            return true;
        } else {
            return ($page->date() >= $since);
        }
    }


    /**
     * @param Page $page                    Page
     * @param ?DateTimeInterface $until     Minimum date for validatation
     *
     * @return bool
     */
    protected function funtil(Page $page, ?DateTimeInterface $until): bool
    {
        if (is_null($until)) {
            return true;
        } else {
            return ($page->date() <= $until);
        }
    }

    protected function fgeo(Page $page, bool $geo)
    {
        if (!$geo) {
            return true;
        } else {
            return $page->isgeo();
        }
    }

    protected function fversion(Page $page, int $version): bool
    {
        if ($version === 0) {
            return true;
        } else {
            return $page->version() === $version;
        }
    }


    /**
     * Search for regex and count occurences
     *
     * @param Page[] $pagelist              list Array of Pages.
     * @param string $regex                 Regex to match.
     * @param string[] $options             Option search, could be `content` `title` `description`.
     *
     * @return Page[] associative array of `Page` objects
     */
    protected function deepsearch(array $pagelist, string $regex, array $options): array
    {
        if ($options['casesensitive']) {
            $case = '';
        } else {
            $case = 'i';
        }
        $regex = '/' . $regex . '/' . $case;
        $pageselected = [];
        foreach ($pagelist as $page) {
            $count = 0;
            if ($options['content']) {
                foreach ($page->contents() as $content) {
                    $count += preg_match($regex, $page->$content());
                }
            }
            if ($options['other']) {
                $count += preg_match($regex, $page->body());
                $count += preg_match($regex, $page->css());
                $count += preg_match($regex, $page->javascript());
            }
            if ($options['id']) {
                $count += preg_match($regex, $page->id());
            }
            if ($options['title']) {
                $count += preg_match($regex, $page->title());
            }
            if ($options['description']) {
                $count += preg_match($regex, $page->description());
            }
            if ($count !== 0) {
                $pageselected[$page->id()] = $page;
            }
        }
        return $pageselected;
    }

    /**
     * Create a page
     *
     * @param array $datas                  Page's datas
     * @return Page                         V1 or V2 depending of config file setting
     */
    public function newpage(array $datas = []): Page
    {
        $pageversion = Config::pageversion();
        switch ($pageversion) {
            case Page::V1:
                return new Pagev1($datas);

            case Page::V2:
                return new Pagev2($datas);
        }
        throw new DomainException("Invalid page version allowed to be set in config");
    }


    /**
     * This function will check for page version in datas and will retrun coresponding page version object
     * If no version is specified and `content` field is not used, it will return Pagev1
     *
     * @param array|object $datas           Page's datas
     * @return Page                         V1 or V2
     * @throws RangeException               If page version is defined but out of range
     */
    public function parsepage($datas = []): Page
    {
        $metadatas = is_object($datas) ? get_object_vars($datas) : $datas;
        if (isset($metadatas['version'])) {
            switch ($metadatas['version']) {
                case Page::V1:
                    return new Pagev1($datas);

                case Page::V2:
                    return new Pagev2($datas);
            }
            throw new RangeException('Version is specified but out of range');
        } else {
            return new Pagev1($datas);
        }
    }
}
