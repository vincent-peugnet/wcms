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
use Wcms\Exception\Database\Notfoundexception as DatabaseNotfoundexception;
use Wcms\Exception\Databaseexception;
use Wcms\Exception\Filesystemexception;
use Wcms\Exception\Filesystemexception\Notfoundexception;

class Modelpage extends Modeldb
{
    public const RESERVED_IDS = [
        'media',
        'assets',
    ];

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
     * @return array<string, Page>          of Pages objects as `id => Page`
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
     *
     * @throws RuntimeException if page ID is illegal
     * @throws Databaseexception if error occured whiling saving document
     */
    public function add(Page $page): void
    {
        if (in_array($page->id(), self::RESERVED_IDS)) {
            $id = $page->id();
            throw new RuntimeException("'$id' is a reserved page ID");
        }
        $pagedata = new Document($page->dry());
        $pagedata->setId($page->id());
        $this->storedoc($pagedata);
    }

    /**
     * Obtain a page object from the database
     *
     * @param Page|string $id               could be an Page object or a id string
     *
     * @return Page                         The Page object
     *
     * @throws DatabaseNotfoundexception    If page is'nt found
     * @throws RangeException               If page version is specified but invalid
     */
    public function get($id): Page
    {
        if ($id instanceof Page) {
            $id = $id->id();
        }
        if (!is_string($id)) {
            throw new InvalidArgumentException("argument of Modelpage->get() should be a ID string or Page");
        }

        $pagedata = $this->repo->findById($id);
        if ($pagedata === false) {
            throw new DatabaseNotfoundexception("Could not find Page with the following ID: '$id'");
        }
        return $this->parsepage($pagedata);
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
     * Get all the existing pages that are called in css templating
     * (the last element is the farthest of the chain)
     *
     * @param Page $page                    page to retrieve css templates
     *
     * @param array<string, Page> $templates
     *
     * @return array<string, Page>          array of pages with ID as index
     *
     * @throws RuntimeException             If a loop occured in templates
     */
    public function getpagecsstemplates(Page $page, array $templates = []): array
    {
        $templatecss = $page->templatecss() ?? $page->templatebody(); // null CSS template: same as body template
        if (empty($templatecss)) {
            return [];
        }
        if ($templatecss === $page->id() || key_exists($templatecss, $templates)) {
            throw new RuntimeException('there is a loop in CSS templates');
        }

        try {
            $template = $this->get($templatecss);
            $templates[$template->id()] = $template;
            $templates = array_merge($templates, $this->getpagecsstemplates($template, $templates));
        } catch (Databaseexception | RangeException $e) {
            // This mean page do not exist or is invalid
            Logger::errorex($e);
        }

        return $templates;
    }

    /**
     * Get all the existing pages that are called in Javascript templating
     *
     * @param Page $page                    page to retrieve JS templates
     *
     * @param array<string, Page> $templates
     *
     * @return array<string, Page>          array of pages with ID as index
     *
     * @throws RuntimeException             If a loop occured in templates
     */
    public function getpagejavascripttemplates(Page $page, array $templates = []): array
    {
        $templatejs = $page->templatejavascript() ?? $page->templatebody();
        if (empty($templatejs)) {
            return [];
        }
        if ($templatejs === $page->id() || key_exists($templatejs, $templates)) {
            throw new RuntimeException('there is a loop in Javascript templates');
        }

        try {
            $template = $this->get($templatejs);
            $templates[$template->id()] = $template;
            $templates = array_merge($templates, $this->getpagejavascripttemplates($template, $templates));
        } catch (Databaseexception | RangeException $e) {
            // This mean page do not exist or is invalid
            Logger::errorex($e);
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
     * @throws Databaseexception            If deletion from database failed
     *
     * @throws Filesystemexception          If deleting rendered files failed (which is less severe)
     */
    public function delete(Page $page): void
    {
        if (!$this->repo->delete($page->id())) {
            throw new Databaseexception('Impossible to delete document from database');
        }
        $this->removecache($page->id());
    }

    /**
     * Delete rendered CSS, JS and HTML files associated with given Page
     *
     * @param string|Page $id
     *
     * @throws Filesystemexception          If a file deletion failure occurs
     */
    public function removecache($id): void
    {
        if ($id instanceof Page) {
            $id = $id->id();
        }
        if (!is_string($id)) {
            throw new InvalidArgumentException("argument should be a ID string or Page");
        }

        $files = ['.css', '.quick.css', '.js'];
        foreach ($files as $file) {
            try {
                Fs::deletefile(Model::ASSETS_RENDER_DIR . $id . $file);
            } catch (Notfoundexception $e) {
                // do nothing, this means file is already deleted
            }
        }
        try {
            Fs::deletefile(Model::HTML_RENDER_DIR . $id . '.html');
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
     * @param Page $page                    The page that is going to be updated
     *
     * @throws Databaseexception            in case of error
     */
    public function update(Page $page): void
    {
        $pagedata = new Document($page->dry());
        $pagedata->setId($page->id());
        $this->updatedoc($pagedata);
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
        $this->update($page);
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
     * A page need to be rendered if:
     *
     * 1. render file(s) are missing
     * 2. edit date is more recent than render date
     * 3. if the templatebody is set, exist and has been updated
     * 4. cache time to live is reached
     *
     * @param Page $page                    Page to be checked
     *
     * @param int<1, max> $level
     *
     * @return bool                         true if the page need to be rendered otherwise false
     */
    public function needtoberendered(Page $page, int $level = 4): bool
    {
        if ($level < 1) {
            throw new DomainException('minimum level is 1');
        }

        if (
            !file_exists(self::HTML_RENDER_DIR . $page->id() . '.html')
            || !file_exists(self::ASSETS_RENDER_DIR . $page->id() . '.css')
            || !file_exists(self::ASSETS_RENDER_DIR . $page->id() . '.js')
        ) {
            return true;
        }
        if ($level >= 2 && $page->daterender() <= $page->datemodif()) {
            return true;
        }

        if ($level >= 3 && !empty($page->templatebody())) {
            try {
                $bodytemplate = $this->get($page->templatebody());
                if ($page->daterender() <= $bodytemplate->datemodif()) {
                    return true;
                }
            } catch (RuntimeException $e) {
                Logger::errorex($e);
            }
        }

        $cachettl = $page->cachettl() === null ? Config::cachettl() : $page->cachettl();
        if ($level >= 4 && $cachettl !== -1) {
            $maxttl = $page->daterender()->getTimestamp() + $cachettl;
            if (time() > $maxttl) {
                return true;
            }
        }

        return false;
    }

    /**
     * Render given page
     * Write HTML, CSS and JS files
     * update linkto property
     * update external links
     *
     * @param Page $page
     *
     * @param ?Serviceurlchecker $urlchecker
     *
     * @return Page rendered $page
     *
     * @throws Runtimeexception if writing files to filesystem failed
     */
    public function renderpage(Page $page, AltoRouter $router, ?Serviceurlchecker $urlchecker = null): Page
    {
        $now = new DateTimeImmutable("now", timezone_open("Europe/Paris"));

        $params = [
            $router,
            $this,
            Config::externallinkblank(),
            Config::internallinkblank(),
            Config::titlefromalt(),
            $urlchecker
        ];

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
        $page->setexternallinks($renderengine->urls());
        $page->setpostprocessaction($renderengine->postprocessaction());

        return $page;
    }

    /**
     * Render page's JS and CSS templates if they need to
     * If a page template do not exist, it won't trigger an error
     *
     * @param Page $page                    page to check templates
     *
     * @throws RuntimeException if writing render to filesystem failed or if there is a loop
     */
    public function templaterender(Page $page, AltoRouter $router): void
    {
        $templates = array_merge(
            $this->getpagecsstemplates($page),
            $this->getpagejavascripttemplates($page)
        );
        foreach ($templates as $template) {
            if ($this->needtoberendered($template, 2)) { // we don't care of content or body here
                $template = $this->renderpage($template, $router, null);
                $this->update($template);
            }
        }
    }








    // _____________________________ FILTERING & SORTING _____________________________


    /**
     * Main page list filtering and sorting tool
     *
     * @param Page[] $pagelist             of Pages objects as `id => Page`
     * @param Opt $opt
     *
     * @param string $regex                 Regex to match.
     * @param array<string, bool> $searchopt
     * Option search, could be `content` `title` `description`
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
    protected function pagecompare(Page $page1, Page $page2, string $method = 'id', int $order = 1): int
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
    protected function pagelistsort(array &$pagelist, string $sortby, int $order = 1): bool
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

    protected function fgeo(Page $page, bool $geo): bool
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
     * @param array<string, bool> $options             Option search, could be `content` `title` `description`.
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
        $regex = '/' . preg_quote($regex, '/') . '/';
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
     * @param array<string, mixed> $datas   Page's datas
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
     * @param mixed[]|object $datas         Page's datas
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
        } elseif (isset($metadatas['content'])) {
            return new Pagev2($datas);
        } else {
            return new Pagev1($datas);
        }
    }
}
