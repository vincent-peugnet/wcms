<?php

namespace Wcms;

class Controllerhome extends Controllerpage
{
    /** @var Modelhome */
    protected $modelhome;
    /** @var Opt */
    protected $opt;
    /** @var Optlist */
    protected $optlist;

    public function __construct($render)
    {
        parent::__construct($render);
        $this->modelhome = new Modelhome;
    }




    public function desktop()
    {
        if ($this->user->isvisitor() && Config::homepage() === 'redirect' && !empty(Config::homeredirect())) {
            $this->routedirect('pageread/', ['page' => Config::homeredirect()]);
        } else {


            $pagelist = $this->modelhome->getlister();
            $this->opt = $this->modelhome->optinit($pagelist);

            $vars['colors'] = new Colors($this->opt->taglist());

            $deepsearch = $this->deepsearch();

            $idlistfilter = $this->modelhome->filter($pagelist, $this->opt);
            $pagelistfilter = $this->modelhome->pagelistfilter($pagelist, $idlistfilter);
            $pagelistdeep = $this->modelhome->deepsearch($pagelistfilter, $deepsearch['regex'] , $deepsearch['searchopt']);
            $pagelistsort = $this->modelhome->sort($pagelistdeep, $this->opt);
            $vars['pagelistopt'] = $pagelistsort;


            $vars['columns'] = $this->modelhome->setcolumns($this->user->columns());

            $vars['faviconlist'] = $this->mediamanager->listfavicon();
            $vars['thumbnaillist'] = $this->mediamanager->listthumbnail();
            $vars['editorlist'] = $this->usermanager->getlisterbylevel(2, '>=');
            $vars['user'] = $this->user;
            $vars['opt'] = $this->opt;
            $vars['deepsearch'] = $deepsearch['regex'];
            $vars['searchopt'] = $deepsearch['searchopt'];
            $vars['display'] = $_GET['display'] ?? 'list';
            
            if($vars['display'] === 'map') {
                $vars['layout'] = $_GET['layout'] ?? 'cose-bilkent';
                $vars['hideorphans'] = boolval($_GET['hideorphans'] ?? false);
                $datas = $this->modelhome->cytodata($pagelistsort, $vars['layout'], $vars['hideorphans']);
                $vars['json'] = json_encode($datas, JSON_PRETTY_PRINT);
            }

            $vars['footer'] = ['version' => getversion(), 'total' => count($pagelist), 'database' => Config::pagetable()];

            $this->listquery($pagelist);

            $vars['optlist'] = $this->optlist ?? null;

            $this->showtemplate('home', $vars);
        }
    }

    /**
     * Look for GET deepsearch datas and transform it an array
     * 
     * @return array containing `string $regex` and `array $searchopt`
     */
    public function deepsearch() : array
    {
        if (!isset($_GET['search'])) {
            $searchopt = ['title' => 1, 'description' => 1, 'content' => 1, 'other' => 0, 'casesensitive' => 0];
        } else {
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
            $this->optlist = $this->modelhome->Optlistinit($pagelist);
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
            $colors = new Colors();
            $colors->hydrate($_POST);
            $colors->tocss();
            $colors->writecssfile();
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
            $pagelist = $this->modelhome->getlister();
            foreach ($pagelist as $page) {
                $this->renderpage($page);
                $this->pagemanager->update($page);
            }
        }
        $this->routedirect('home');
    }

    public function bookmark()
    {
        if ($this->user->iseditor() && isset($_POST['action']) && isset($_POST['id']) && !empty($_POST['id'])) {
            if ($_POST['action'] == 'add' && isset($_POST['query'])) {
                if (isset($_POST['user']) && $_POST['user'] == $this->user->id()) {
                    $usermanager = new Modeluser();
                    $user = $usermanager->get($_POST['user']);
                    $user->addbookmark($_POST['id'], $_POST['query']);
                    $usermanager->add($user);
                } else {
                    Config::addbookmark($_POST['id'], $_POST['query']);
                    Config::savejson();
                }
            } elseif ($_POST['action'] == 'del') {
                if(isset($_POST['user']) && $_POST['user'] == $this->user->id()) {
                    $usermanager = new Modeluser();
                    $user = $usermanager->get($_POST['user']);
                    foreach ($_POST['id'] as $id) {
                        $user->deletebookmark($id);
                    }
                    $usermanager->add($user);
                } else {
                    foreach ($_POST['id'] as $id) {
                        Config::deletebookmark($id);
                    }
                    Config::savejson();
                }
            }
        }
        $this->routedirect('home');
    }

    public function multi()
    {
        if(isset($_POST['action']) && $this->user->issupereditor() && !empty($_POST['pagesid'])) {
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
        }
        $this->routedirect('home');
    }

    public function multiedit()
    {
        if (isset($_POST['pagesid'])) {
            $datas = $_POST['datas']?? [];
            $datas = array_filter($datas, function ($var) {
                return $var !== "";
            });
            $datas = array_map(function ($value) {
                if($value === "!") {
                    return "";
                } else {
                    return $value;
                }
            }, $datas);
            $reset = $_POST['reset'] ?? [];
            $addtag = $_POST['addtag'] ?? '';
            $addauthor = $_POST['addauthor'] ?? '';
            foreach ($_POST['pagesid'] as $id) {
                $this->pagemanager->pageedit($id, $datas, $reset, $addtag, $addauthor);
            }
        }
    }

    public function multirender()
    {
        $pagelist = $_POST['pagesid'] ?? [];
        $pagelist = $this->pagemanager->getlisterid($pagelist);
        foreach ($pagelist as $page) {
            $page = $this->renderpage($page);
            $this->pagemanager->update($page);
        }

    }

    public function multidelete()
    {
        if(isset($_POST['confirmdelete']) && $_POST['confirmdelete']) {
            $pagelist = $_POST['pagesid'] ?? [];
            foreach ($pagelist as $id) {
                $this->pagemanager->delete($id);
            }
        }        
    }
}

?>
