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
        if ($this->user->isvisitor() && Config::homepage() === 'redirect' && Config::homeredirect() !== null) {
            $this->routedirect('pageread/', ['page' => Config::homeredirect()]);
        } else {


            $table = $this->modelhome->getlister();
            $this->opt = $this->modelhome->optinit($table);

            $colors = new Colors($this->opt->taglist());

            $table2 = $this->modelhome->table2($table, $this->opt);

            $columns = $this->modelhome->setcolumns($this->user->columns());

            $vars = ['user' => $this->user, 'table2' => $table2, 'opt' => $this->opt, 'columns' => $columns, 'faviconlist' => $this->mediamanager->listfavicon(), 'editorlist' => $this->usermanager->getlisterbylevel(2, '>='), 'colors' => $colors];
            $vars['footer'] = ['version' => getversion(), 'total' => count($table), 'database' => Config::pagetable()];

            if (isset($_POST['query']) && $this->user->iseditor()) {
                $datas = array_merge($_POST, $_SESSION['opt']);
                $this->optlist = $this->modelhome->Optlistinit($table);
                $this->optlist->hydrate($datas);
                $vars['optlist'] = $this->optlist;
            }

            $this->showtemplate('home', $vars);
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
