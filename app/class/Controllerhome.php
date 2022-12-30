<?php

namespace Wcms;

use RuntimeException;
use Wcms\Exception\Database\Databaseexception;

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
        if ($this->user->isvisitor() && Config::homepage() === 'redirect' && !empty(Config::homeredirect())) {
            $this->routedirect('pageread/', ['page' => Config::homeredirect()]);
        } else {
            $pagelist = $this->pagemanager->pagelist();


            $this->opt = new Opt();
            $this->opt->settaglist($pagelist);
            $this->opt->setauthorlist($pagelist);
            $this->opt->setpageidlist($pagelist);
            $this->opt->submit();

            try {
                $vars['colors'] = new Colors(Model::COLORS_FILE, $this->opt->taglist());
            } catch (RuntimeException $e) {
                Model::sendflashmessage("Error while generating display colors", Model::FLASH_ERROR);
            }

            $publicbookmarks = $this->bookmarkmanager->getlisterpublic();
            $personalbookmarks = $this->bookmarkmanager->getlisterbyuser($this->user);
            $queryaddress = $this->opt->getaddress();
            $bookmarks = array_merge($publicbookmarks, $personalbookmarks);

            $vars['editablebookmarks'] = $personalbookmarks;
            if ($this->user->isadmin()) {
                $vars['editablebookmarks'] += $publicbookmarks;
            }
            $vars['publicbookmarks'] = $publicbookmarks;
            $vars['personalbookmarks'] = $personalbookmarks;
            $vars['queryaddress'] = $queryaddress;
            $vars['matchedbookmarks'] = $this->modelhome->matchedbookmarks($bookmarks, $queryaddress);

            $deepsearch = $this->deepsearch();

            $vars['pagelistopt'] = $this->pagemanager->pagetable(
                $pagelist,
                $this->opt,
                $deepsearch['regex'],
                $deepsearch['searchopt']
            );


            $vars['columns'] = $this->modelhome->setcolumns($this->user->columns());


            $vars['faviconlist'] = $this->mediamanager->listfavicon();
            $vars['thumbnaillist'] = $this->mediamanager->listthumbnail();
            $vars['editorlist'] = $this->usermanager->getlisterbylevel(2, '>=');
            $vars['user'] = $this->user;
            $vars['opt'] = $this->opt;
            $vars['deepsearch'] = $deepsearch['regex'];
            $vars['searchopt'] = $deepsearch['searchopt'];
            $vars['display'] = $_GET['display'] ?? 'list';

            if ($vars['display'] === 'map') {
                $vars['layout'] = $_GET['layout'] ?? 'cose-bilkent';
                $vars['showorphans'] = boolval($_GET['showorphans'] ?? false);
                $vars['showredirection'] = boolval($_GET['showredirection'] ?? false);
                $datas = $this->modelhome->cytodata(
                    $vars['pagelistopt'],
                    $vars['layout'],
                    $vars['showorphans'],
                    $vars['showredirection']
                );
                $vars['json'] = json_encode($datas, JSON_PRETTY_PRINT);
            }

            $vars['footer'] = [
                'version' => getversion(),
                'total' => count($pagelist),
                'database' => Config::pagetable()
            ];

            $this->listquery();

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
        if (isset($_POST['query']) && $this->user->iseditor()) {
            $datas = array_merge($_POST, $_SESSION['opt']);
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
                $user->hydrate($_POST);
                $this->usermanager->add($user);
                $this->usermanager->writesession($user);
                Model::sendflashmessage('Display settings successfully saved', Model::FLASH_SUCCESS);
            } catch (Databaseexception $e) {
                Model::sendflashmessage('Error while trying to save display settings', Model::FLASH_ERROR);
            }
        }
        $this->routedirect('home');
    }

    public function colors()
    {
        if (isset($_POST['tagcolor']) && $this->user->issupereditor()) {
            try {
                $colors = new Colors(Model::COLORS_FILE);
                $colors->update($_POST['tagcolor']);
            } catch (RuntimeException $e) {
                Model::sendflashmessage("Error while saving display colors", Model::FLASH_ERROR);
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
                        $this->routedirect('pageread/', ['page' => $_POST['id']]);
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

    /**
     * Render every pages in the database
     */
    public function renderall()
    {
        if ($this->user->iseditor()) {
            $pagelist = $this->pagemanager->pagelist();
            $count = 0;
            foreach ($pagelist as $page) {
                $page = $this->pagemanager->renderpage($page, $this->router);
                if ($this->pagemanager->update($page)) {
                    $count++;
                }
            }
            $total = count($pagelist);
            $this->sendstatflashmessage($count, $total, 'pages have been rendered');
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
            Model::sendflashmessage('Please select some pages to ' . $action, 'warning');
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

    public function multirender()
    {
        $pagelist = $_POST['pagesid'] ?? [];
        $total = count($pagelist);
        $pagelist = $this->pagemanager->pagelistbyid($pagelist);
        $count = 0;
        foreach ($pagelist as $page) {
            $page = $this->pagemanager->renderpage($page, $this->router);
            if ($this->pagemanager->update($page)) {
                $count++;
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
                if ($this->pagemanager->delete($id)) {
                    $count++;
                }
            }
            $this->sendstatflashmessage($count, $total, 'pages have been deleted');
        } else {
            Model::sendflashmessage('Confirm delete has not been cheked', 'warning');
        }
    }
}
