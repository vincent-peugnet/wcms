<?php

namespace Wcms;

use RuntimeException;
use Wcms\Exception\Filesystemexception;
use Wcms\Exception\Databaseexception;
use Wcms\Exception\Filesystemexception\Notfoundexception;

class Controllerhome extends Controller
{
    /** @var Modelhome */
    protected $modelhome;
    /** @var Opt */
    protected $opt;
    /** @var Optlist */
    protected $optlist;
    /** @var Modelbookmark */
    protected $bookmarkmanager;
    /** @var Modelmedia */
    protected $mediamanager;

    public function __construct($router)
    {
        parent::__construct($router);
        $this->modelhome = new Modelhome();
        $this->bookmarkmanager = new Modelbookmark();
        $this->mediamanager = new Modelmedia();
    }




    public function desktop()
    {
        if ($this->user->isvisitor()) {
            if (Config::homepage() === 'redirect' && !empty(Config::homeredirect())) {
                $this->routedirect('pageread', ['page' => Config::homeredirect()]);
            } else {
                $this->showtemplate('connect', ['route' => 'home']);
            }
        } else {
            $display = $_GET['display'] ?? 'list';

            $pagelist = $this->pagemanager->pagelist();


            $this->opt = new Opt();
            $this->opt->settaglist($pagelist);
            $this->opt->setauthorlist($pagelist);
            $this->opt->setpageidlist($pagelist);
            $this->opt->submit();

            try {
                $servicetags = new Servicetags();
                $vars['colors'] = $servicetags->synctags($this->opt->taglist());
            } catch (RuntimeException $e) {
                $this->sendflashmessage("Error while generating display colors", self::FLASH_ERROR);
                Logger::errorex($e);
            }

            $publicbookmarks = $this->bookmarkmanager->getlisterpublic();
            $personalbookmarks = $this->bookmarkmanager->getlisterbyuser($this->user);
            $queryaddress = $this->opt->getaddress();
            $bookmarks = array_merge($publicbookmarks, $personalbookmarks);

            $vars['editablebookmarks'] = $personalbookmarks;
            $vars['matchedbookmarks'] = $this->modelhome->matchedbookmarks($personalbookmarks, $queryaddress);
            if ($this->user->isadmin()) {
                $vars['editablebookmarks'] += $publicbookmarks;
                $vars['matchedbookmarks'] += $this->modelhome->matchedbookmarks($publicbookmarks, $queryaddress);
            }
            $vars['publicbookmarks'] = $publicbookmarks;
            $vars['personalbookmarks'] = $personalbookmarks;
            $vars['queryaddress'] = $queryaddress;

            $deepsearch = $this->deepsearch();

            $pagelistopt = $this->pagemanager->pagetable(
                $pagelist,
                $this->opt,
                $deepsearch['regex'],
                $deepsearch['searchopt']
            );


            $vars['columns'] = $this->user->checkedcolumns();


            $vars['faviconlist'] = $this->mediamanager->listfavicon();
            $vars['thumbnaillist'] = $this->mediamanager->listthumbnail();
            $vars['editorlist'] = $this->usermanager->getlisterbylevel(2, '>=');
            $vars['user'] = $this->user;
            $vars['opt'] = $this->opt;
            $vars['deepsearch'] = $deepsearch['regex'];
            $vars['searchopt'] = $deepsearch['searchopt'];
            $vars['display'] = $display;
            $vars['urlchecker'] = Config::urlchecker();
            $vars['hiddencolumncount'] = count(User::HOME_COLUMNS) - count($this->user->columns());

            if ($display === 'graph') {
                $graph = $this->servicesession->getgraph();
                $graph->hydrate($_GET);
                $this->servicesession->setgraph($graph);
                $datas = $this->modelhome->cytodata($pagelistopt, $graph);
                $vars['json'] = json_encode($datas);
                $vars['graph'] = $graph;
            }

            if ($display === 'map') {
                if (!$this->opt->geo()) {
                    $geopt = new Opt(['geo' => true]);
                    $geopages = $this->pagemanager->pagetable($pagelistopt, $geopt);
                } else {
                    $geopages = $pagelistopt;
                }
                $vars['mapcounter'] = count($geopages);
                $geopages = array_map(function (Page $page) {
                    $data = $page->drylist(['id', 'title', 'latitude', 'longitude']);
                    $data['read'] = $this->generate('pageread', ['page' => $page->id()]);
                    $data['edit'] = $this->generate('pageedit', ['page' => $page->id()]);
                    return $data;
                }, $geopages);
                $geopages = array_values($geopages);
                $vars['json'] = json_encode($geopages);
            }

            $vars['pagelistopt'] = $pagelistopt;
            $vars['footer'] = [
                'version' => getversion(),
                'total' => count($pagelist),
                'database' => Config::pagetable(),
                'pageversion' => Config::pageversion()
            ];

            $this->listquery();

            $optrandom = new Optrandom();
            $optrandom->submit();
            $vars['optrandom'] = $optrandom;

            $optmap = new Optmap();
            $optmap->submit();
            $vars['optmap'] = $optmap;

            $vars['optlist'] = $this->optlist;

            $this->showtemplate('home', $vars);
        }
    }

    /**
     * Look for GET deepsearch datas and transform it an array
     *
     * @return array containing `string $regex` and `array $searchopt`
     */
    protected function deepsearch(): array
    {
        if (!isset($_GET['search'])) {
            $searchopt = [
                'id' => 1,
                'title' => 1,
                'description' => 1,
                'content' => 1,
                'other' => 0,
                'casesensitive' => 0
            ];
        } else {
            $searchopt['id'] = $_GET['id'] ?? 0;
            $searchopt['title'] = $_GET['title'] ?? 0;
            $searchopt['description'] = $_GET['description'] ?? 0;
            $searchopt['content'] = $_GET['content'] ?? 0;
            $searchopt['other'] = $_GET['other'] ?? 0;
            $searchopt['casesensitive'] = $_GET['case'] ?? 0;
        }
        $regex = $_GET['search'] ?? '';
        return ['regex' => $regex, 'searchopt' => $searchopt];
    }

    protected function listquery(): void
    {
        if (isset($_POST['listquery']) && $this->user->iseditor()) {
            $datas = array_merge($_POST, $this->servicesession->getopt());
            $this->optlist = new Optlist($datas);
            if (!empty($this->optlist->bookmark())) {
                $this->optlist->resetall();
            }
        }
    }

    public function columns()
    {
        if (isset($_POST['columns']) && $this->user->iseditor()) {
            try {
                $user = $this->usermanager->get($this->user->id());
                $user->setcolumns($_POST['columns']);
                $this->usermanager->add($user);
                $this->sendflashmessage('Display settings successfully saved', self::FLASH_SUCCESS);
            } catch (Databaseexception $e) {
                $this->sendflashmessage('Error while trying to save display settings', self::FLASH_ERROR);
            }
        }
        $this->routedirect('home');
    }

    public function colors()
    {
        if ($this->user->issupereditor()) {
            try {
                $servicetags = new Servicetags();
                $servicetags->updatecolors($_POST);
            } catch (RuntimeException $e) {
                $this->sendflashmessage("Error while saving display colors", self::FLASH_ERROR);
                Logger::errorex($e);
            }
        }
        $this->routedirect('home');
    }

    public function search()
    {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            if (isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'read':
                        $this->routedirect('pageread', ['page' => $_POST['id']]);
                        break;

                    case 'edit':
                        $this->routedirect('pageedit', ['page' => $_POST['id']]);
                        break;
                }
            }
        } else {
            $this->routedirect('home');
        }
    }

    public function flushrendercache(): void
    {
        if (!$this->user->issupereditor()) {
            http_response_code(304);
            $this->showtemplate('forbidden');
            exit;
        }
        try {
            $this->pagemanager->flushrendercache();
            $this->sendflashmessage('Render cache successfully deleted', self::FLASH_SUCCESS);
        } catch (RuntimeException $e) {
            $this->sendflashmessage($e->getMessage(), self::FLASH_ERROR);
            Logger::errorex($e);
        }
        $this->routedirect('home');
    }

    public function flushurlcache(): void
    {
        if (!$this->user->issupereditor()) {
            http_response_code(304);
            $this->showtemplate('forbidden');
            exit;
        }
        try {
            Fs::deletefile(Model::URLS_FILE);
            $this->sendflashmessage('URL cache successfully deleted', self::FLASH_SUCCESS);
        } catch (Notfoundexception $e) {
            $this->sendflashmessage('URL cache is already deleted ' . $e->getMessage(), self::FLASH_WARNING);
        } catch (RuntimeException $e) {
            $fserror = $e->getMessage();
            $this->sendflashmessage("Error while trying to flush page render cache: $fserror", self::FLASH_ERROR);
            Logger::errorex($e);
        }
        $this->routedirect('home');
    }

    /**
     * Remove unused URLs from cache
     */
    public function cleanurlcache(): void
    {
        if (!$this->user->issupereditor()) {
            http_response_code(304);
            $this->showtemplate('forbidden');
            exit;
        }
        try {
            $urlchecker = new Serviceurlchecker(0);
            $removed = $urlchecker->cleancache($this->pagemanager);
            $urlchecker->savecache();
            $this->sendflashmessage("$removed unused url(s) where removed from cache.", self::FLASH_SUCCESS);
        } catch (RuntimeException $e) {
            $this->sendflashmessage($e, self::FLASH_ERROR);
            Logger::errorex($e);
        }
        $this->routedirect('home');
    }

    public function multi()
    {
        if (isset($_POST['action']) && $this->user->issupereditor() && !empty($_POST['pagesid'])) {
            switch ($_POST['action']) {
                case 'edit':
                    $this->multiedit();
                    break;

                case 'render':
                    $this->multirender();
                    break;

                case 'delete':
                    $this->multidelete();
                    break;
            }
        } else {
            $action = $_POST['action'] ?? 'edit';
            $this->sendflashmessage('Please select some pages to ' . $action, self::FLASH_WARNING);
        }
        $this->routedirect('home');
    }

    public function multiedit()
    {
        $pagelist = $_POST['pagesid'] ?? [];
        $datas = $_POST['datas'] ?? [];
        $datas = array_filter($datas, function ($var) {
            return $var !== "";
        });
        $datas = array_map(function ($value) {
            if ($value === "!") {
                return "";
            } else {
                return $value;
            }
        }, $datas);
        $reset = $_POST['reset'] ?? [];
        $addtag = $_POST['addtag'] ?? '';
        $addauthor = $_POST['addauthor'] ?? '';
        $count = 0;
        $total = 0;
        foreach ($pagelist as $id) {
            $total++;
            try {
                $this->pagemanager->pageedit($id, $datas, $reset, $addtag, $addauthor);
                $count++;
            } catch (RuntimeException $e) {
                Logger::error($e);
            }
        }
        $this->sendstatflashmessage($count, $total, 'pages have been edited');
    }

    public function multirender(): void
    {
        $pagelist = $_POST['pagesid'] ?? [];
        $total = count($pagelist);
        $pagelist = $this->pagemanager->pagelistbyid($pagelist);
        $count = 0;
        foreach ($pagelist as $page) {
            try {
                $page = $this->pagemanager->renderpage(
                    $page,
                    $this->router,
                    Config::urlchecker() ? new Serviceurlchecker(0) : 0
                );
                if ($this->pagemanager->update($page)) {
                    $count++;
                }
            } catch (RuntimeException $e) {
                Logger::errorex($e);
            }
        }
        $this->sendstatflashmessage($count, $total, 'pages have been rendered');
    }

    public function multidelete()
    {
        if (isset($_POST['confirmdelete']) && $_POST['confirmdelete']) {
            $pagelist = $_POST['pagesid'] ?? [];
            $total = count($pagelist);
            $count = 0;
            foreach ($pagelist as $id) {
                if ($this->pagemanager->delete(new Pagev2(['id' => $id]))) {
                    $count++;
                }
            }
            $this->sendstatflashmessage($count, $total, 'pages have been deleted');
        } else {
            $this->sendflashmessage('Confirm delete has not been cheked', self::FLASH_WARNING);
        }
    }
}
