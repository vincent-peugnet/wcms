<?php

namespace Wcms;

use AltoRouter;
use JamesMoss\Flywheel\Document;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use RuntimeException;

class Modelpage extends Modeldb
{
    public const SECURE_LEVELS = [
        0 => 'public',
        1 => 'private',
        2 => 'not_published'
    ];

    protected $pagelist = [];

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
                $this->pagelist[$pagedata->id] = new Page($pagedata);
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
            $pagelist[$id] = new Page($pagedata);
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
        return $this->repo->store($pagedata);
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
            if ($pagedata !== false) {
                return new Page($pagedata);
            } else {
                throw new RuntimeException("Could not find Page with the following ID: \"$id\"");
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

        $ext = substr(strrchr($_FILES['pagefile']['name'], '.'), 1);
        if ($ext !== 'json') {
            return false;
        }

        $files = $_FILES;

        $json = file_get_contents($_FILES['pagefile']['tmp_name']);
        $pagedata = json_decode($json, true);

        if ($pagedata === false) {
            return false;
        }

        $page = new Page($pagedata);

        return $page;
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
            if (in_array('recursivecss', $page->templateoptions())) {
                $templates = array_merge($this->getpagecsstemplates($template), $templates);
            }
        }
        return $templates;
    }

    /**
     * Delete a page and it's linked rendered html and css files
     *
     * @param Page|string $page             could be an Page object or a id string
     *
     * @return bool                         true if success otherwise false
     */
    public function delete($page): bool
    {
        if ($page instanceof Page) {
            $page = $page->id();
        }
        if (is_string($page)) {
            $this->unlink($page);
            return $this->repo->delete($page);
        } else {
            return false;
        }
    }

    /**
     * Delete rendered CSS and HTML files
     *
     * @param string $pageid
     */
    public function unlink(string $pageid)
    {
        $files = ['.css', '.quick.css', '.js'];
        foreach ($files as $file) {
            if (file_exists(Model::RENDER_DIR . $pageid . $file)) {
                unlink(Model::RENDER_DIR . $pageid . $file);
            }
        }
        if (file_exists(Model::HTML_RENDER_DIR . $pageid . '.html')) {
            unlink(Model::HTML_RENDER_DIR . $pageid . '.html');
        }
    }

    /**
     * Update a page in the database
     *
     * @todo Check if page already exist before updating ?
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
        return $this->repo->store($pagedata);
    }

    public function combine(Page $pagea, Page $pageb)
    {
        $mergepage = $pagea;
        $merge = [];
        $diff = [];
        foreach ($pagea::TABS as $element) {
            if ($pagea->$element() !== $pageb->$element()) {
                $merge[$element] = compare($pagea->$element(), $pageb->$element());
                $diff[] = $element;
            }
        }
        $mergepage->hydrate($merge);

        return ['diff' => $diff, 'mergepage' => $mergepage];
    }

    // public function diffpageelement(Page $pagea, Page $pageb)
    // {
    //  $diff = [];
    //  foreach ($pagea::TABS as $element) {
    //      if($pagea->$element() !== $pageb->$element()) {
    //          $diff[] = $element;
    //      }
    //  }
    //  return $diff;
    // }

    /**
     * @param string[] $taglist             list of tags
     * @param Page[] $pagelist              list of Page
     *
     * @return array                        list of tags each containing list of id
     */
    public function tagpagelist(array $taglist, array $pagelist): array
    {
        $tagpagelist = [];
        foreach ($taglist as $tag) {
            $tagpagelist[$tag] = $this->filtertagfilter($pagelist, [$tag]);
        }
        return $tagpagelist;
    }

    public function lasteditedpagelist(int $last, array $pagelist)
    {
        $this->pagelistsort($pagelist, 'datemodif', -1);
        $pagelist = array_slice($pagelist, 0, $last);
        $idlist = [];
        foreach ($pagelist as $page) {
            $idlist[] = $page->id();
        }
        return $idlist;
    }

    /**
     * Edit a page based on meta infos
     *
     * @param string $pageid
     * @param array $datas
     * @param array $reset
     * @param string $addtag
     * @param string $addauthor
     *
     * @throws RuntimeException             When page is not found in the database or update failed
     */
    public function pageedit(string $pageid, array $datas, array $reset, string $addtag, string $addauthor)
    {
        $page = $this->get($pageid);
        $page = $this->reset($page, $reset);
        $page->hydrate($datas);
        $page->addtag($addtag);
        $page->addauthor($addauthor);
        if ($this->update($page)) {
            throw new RuntimeException("Error while trying to update page $pageid");
        }
    }

    /**
     * Reset values of a page
     *
     * @param Page $page                    Page object to be reseted
     * @param array $reset                  List of parameters needing reset
     *
     * @return Page                         The reseted page object
     */
    public function reset(Page $page, array $reset): Page
    {
        $now = new DateTimeImmutable("now", timezone_open("Europe/Paris"));
        if ($reset['tag']) {
            $page->settag([]);
        }
        if ($reset['author']) {
            $page->setauthors([]);
        }
        if ($reset['redirection']) {
            $page->setredirection('');
        }
        if ($reset['date']) {
            $page->setdate($now);
        }
        if ($reset['datemodif']) {
            $page->setdatemodif($now);
        }
        return $page;
    }

    /**
     * Check if a page need to be rendered
     *
     * A page need to be rendered when:
     *
     * - datemodif is after daterender
     * - render files do not exist
     * - body template is used and template datemodif is after page daterender
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
            !file_exists(self::RENDER_DIR . $page->id() . '.css') ||
            !file_exists(self::RENDER_DIR . $page->id() . '.js')
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
     * Generate and write HTML, CSS and JS files
     * update linkto property
     *
     * @param Page $page input
     *
     * @return Page rendered $page
     *
     * @todo This function should not send flash message itself,
     * but throw a RuntimeException instead
     */
    public function renderpage(Page $page, AltoRouter $router): Page
    {
        $now = new DateTimeImmutable("now", timezone_open("Europe/Paris"));

        $renderengine = new Servicerender($router, $this, Config::externallinkblank(), Config::internallinkblank());

        try {
            $html = $renderengine->render($page);
            Fs::dircheck(Model::HTML_RENDER_DIR, true);
            Fs::writefile(Model::HTML_RENDER_DIR . $page->id() . '.html', $html);
            Fs::writefile(Model::RENDER_DIR . $page->id() . '.css', $page->css(), 0664);
            Fs::writefile(Model::RENDER_DIR . $page->id() . '.js', $page->javascript(), 0664);

            $page->setdaterender($now);
            $page->setlinkto($renderengine->linkto());
            $page->setpostprocessaction($renderengine->postprocessaction());
        } catch (RuntimeException $e) {
            Model::sendflashmessage("Error while saving render files", Model::FLASH_ERROR);
            Logger::errorex($e);
        }

        return $page;
    }

    /**
     * Render all other pages that are linked from this page
     */
    public function recursiverender(Page $page, AltoRouter $router): void
    {
        $relatedpages = array_diff($page->linkto(), [$page->id()]);
        foreach ($relatedpages as $pageid) {
            try {
                $page = $this->get($pageid);
                $page = $this->renderpage($page, $router);
                $this->update($page);
            } catch (RuntimeException $e) {
                Logger::errorex($e, true);
            }
        }
    }

    /**
     * Render all page JS and CSS templates if they need to
     *
     * @param Page $page page to check templates
     */
    public function templaterender(Page $page, AltoRouter $router)
    {
        try {
            $templates = $this->getpagecsstemplates($page);
            foreach ($templates as $page) {
                if ($this->needtoberendered($page)) {
                    $page = $this->renderpage($page, $router);
                    $this->update($page);
                }
            }
        } catch (RuntimeException $e) {
            Logger::errorex($e);
        }
        if (!empty($page->templatejavascript())) {
            try {
                $templatejs = $this->get($page->templatejavascript());
                if ($this->needtoberendered($templatejs)) {
                    $templatejs = $this->renderpage($templatejs, $router);
                    $this->update($templatejs);
                }
            } catch (RuntimeException $e) {
                Logger::errorex($e, true);
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
     * @param array $searchopt              Option search, could be `content` `title` `description`.
     *
     * @return Page[]                       associative array of `Page` objects     *
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
                $this->fgeo($page, $opt->geo())
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


    protected function pagecompare($page1, $page2, $method = 'id', $order = 1)
    {
        $result = ($page1->$method('sort') <=> $page2->$method('sort'));
        return $result * $order;
    }

    protected function buildsorter($sortby, $order)
    {
        return function ($page1, $page2) use ($sortby, $order) {
            $result = $this->pagecompare($page1, $page2, $sortby, $order);
            return $result;
        };
    }



    protected function pagelistsort(&$pagelist, $sortby, $order = 1)
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


    /**
     * Search for regex and count occurences
     *
     * @param Page[] $pagelist              list Array of Pages.
     * @param string $regex                 Regex to match.
     * @param string[] $options             Option search, could be `content` `title` `description`.
     *
     * @return array associative array of `Page` objects
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
                $count += preg_match($regex, $page->main());
                $count += preg_match($regex, $page->nav());
                $count += preg_match($regex, $page->aside());
                $count += preg_match($regex, $page->header());
                $count += preg_match($regex, $page->footer());
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
}
