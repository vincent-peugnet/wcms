<?php

namespace Wcms;

use RuntimeException;

class Controllerhome extends Controllerpage
{
    /** @var Modelhome */
    protected $modelhome;
    /** @var Opt */
    protected $opt;
    /** @var Optlist */
    protected $optlist;
    /** @var Modelbookmark */
    protected $bookmarkmanager;

    public function __construct($render)
    {
        parent::__construct($render);
        $this->modelhome = new Modelhome();
        $this->bookmarkmanager = new Modelbookmark();
    }




    public function desktop()
    {
        if ($this->user->isvisitor() && Config::homepage() === 'redirect' && !empty(Config::homeredirect())) {
            $this->routedirect('pageread/', ['page' => Config::homeredirect()]);
        } else {
            $pagelist = $this->modelhome->pagelist();
            $this->opt = $this->modelhome->optinit($pagelist);


            $vars['colors'] = new Colors(Model::COLORS_FILE, $this->opt->taglist());


            $deepsearch = $this->deepsearch();

            $vars['pagelistopt'] = $this->modelhome->pagetable(
                $pagelist,
                $this->opt,
                $deepsearch['regex'],
                $deepsearch['searchopt']
            );


            $vars['columns'] = $this->modelhome->setcolumns($this->user->columns());

            $vars['bookmarks'] = $this->bookmarkmanager->getlister();

            $vars['faviconlist'] = $this->mediamanager->listfavicon();
            $vars['thumbnaillist'] = $this->mediamanager->listthumbnail();
            $vars['editorlist'] = $this->usermanager->getlisterbylevel(2, '>=');
            $vars['user'] = $this->user;
            $vars['opt'] = $this->opt;
            $vars['deepsearch'] = $deepsearch['regex'];
            $vars['searchopt'] = $deepsearch['searchopt'];
            $vars['display'] = $_GET['display'] ?? 'list';
            $vars['queryaddress'] = $this->opt->getaddress();

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

            $this->listquery($pagelist);

            $vars['optlist'] = $this->optlist;

            $this->showtemplate('home', $vars);
        }
    }

    /**
     * Look for GET deepsearch datas and transform it an array
     *
     * @return array containing `string $regex` and `array $searchopt`
     */
    public function deepsearch(): array
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

    public function listquery(array $pagelist)
    {
        if (isset($_POST['query']) && $this->user->iseditor()) {
            $datas = array_merge($_POST, $_SESSION['opt']);
            $this->optlist = new Optlist();
            $this->optlist->hydrate($datas);
            $vars['optlist'] = $this->optlist;
        }
    }

    public function columns()
    {
        if (isset($_POST['columns']) && $this->user->iseditor()) {
            $user = $this->usermanager->get($this->user->id());
            $user->hydrate($_POST);
            $this->usermanager->add($user);
            $this->usermanager->writesession($user);
        }
        $this->routedirect('home');
    }

    public function colors()
    {
        if (isset($_POST['tagcolor']) && $this->user->issupereditor()) {
            $colors = new Colors(Model::COLORS_FILE);
            $colors->update($_POST['tagcolor']);
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
            $pagelist = $this->modelhome->pagelist();
            $count = 0;
            foreach ($pagelist as $page) {
                $page = $this->renderpage($page);
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
            if ($this->pagemanager->pageedit($id, $datas, $reset, $addtag, $addauthor)) {
                $count++;
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
            $page = $this->renderpage($page);
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
