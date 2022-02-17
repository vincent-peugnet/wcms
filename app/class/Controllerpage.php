<?php

namespace Wcms;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use RuntimeException;

class Controllerpage extends Controller
{
    /** @var Page */
    protected $page;
    protected $fontmanager;
    protected $mediamanager;

    public function __construct($router)
    {
        parent::__construct($router);

        $this->fontmanager = new Modelfont();
        $this->mediamanager = new Modelmedia();
    }

    public function setpage(string $id, string $route)
    {
        $cleanid = Model::idclean($id);
        if ($cleanid !== $id) {
            $this->routedirect($route, ['page' => $cleanid]);
        } else {
            $this->page = new Page(['id' => $cleanid]);
        }
    }

    public function importpage()
    {
        if (isset($_SESSION['pageupdate']) && $_SESSION['pageupdate']['id'] == $this->page->id()) {
            $page = new Page($_SESSION['pageupdate']);
            unset($_SESSION['pageupdate']);
        } else {
            $page = $this->pagemanager->get($this->page);
        }
        if ($page !== false) {
            $this->page = $page;
            return true;
        } else {
            return false;
        }
    }

    /**
     * show credentials for unconnected editors for a specific page
     *
     * @param string $route direction to redirect after the connection form
     * @return void
     */
    public function pageconnect(string $route)
    {
        if ($this->user->isvisitor()) {
            $this->showtemplate('connect', ['route' => $route, 'id' => $this->page->id()]);
            exit;
        }
    }


    public function canedit()
    {
        if ($this->user->issupereditor()) {
            return true;
        } elseif ($this->user->isinvite() || $this->user->iseditor()) {
            if (in_array($this->user->id(), $this->page->authors())) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function render($page)
    {
        $this->setpage($page, 'pageupdate');

        if ($this->importpage() && $this->user->iseditor()) {
            if (Config::recursiverender()) {
                $this->recursiverender($this->page);
            }
            $this->page = $this->renderpage($this->page);
            $this->pagemanager->update($this->page);
            $this->templaterender($this->page);
        }
        $this->routedirect('pageread/', ['page' => $this->page->id()]);
    }

    /**
     * Render given page
     *
     * @param Page $page input
     *
     * @return Page rendered $page
     */
    public function renderpage(Page $page): Page
    {
        $now = new DateTimeImmutable("now", timezone_open("Europe/Paris"));

        $renderengine = new Modelrender($this->router);

        $renderengine->render($page);
        $page->setdaterender($now);
        $page->setlinkto($renderengine->linkto());

        return $page;
    }

    /**
     * Render all other pages that are linked from this page
     */
    public function recursiverender(Page $page): void
    {
        $relatedpages = array_diff($page->linkto(), [$page->id()]);
        foreach ($relatedpages as $pageid) {
            $page = $this->pagemanager->get($pageid);
            if ($page !== false && $this->pagemanager->needtoberendered($page)) {
                $page = $this->renderpage($page);
                $this->pagemanager->update($page);
            }
        }
    }

    /**
     * Render all page templated if they need to
     *
     * @param Page $page page to check templates
     */
    public function templaterender(Page $page)
    {
        $relatedpages = $this->pagemanager->getpagecsstemplates($page);
        foreach ($relatedpages as $page) {
            if ($this->pagemanager->needtoberendered($page)) {
                $page = $this->renderpage($page);
                $this->pagemanager->update($page);
            }
        }
    }

    /**
     * @param string $page page ID
     */
    public function read($page)
    {
        $this->setpage($page, 'pageread/');

        $pageexist = $this->importpage();
        $canread = false;
        $filedir = Model::HTML_RENDER_DIR . $page . '.html';

        if ($pageexist) {
            $canread = $this->user->level() >= $this->page->secure();

            // Check page password
            if (!empty($this->page->password())) {
                if (empty($_POST['pagepassword']) || $_POST['pagepassword'] !== $this->page->password()) {
                    $this->showtemplate('pagepassword', ['pageid' => $this->page->id()]);
                    exit;
                }
            }

            if ($this->pagemanager->needtoberendered($this->page)) {
                if (Config::recursiverender()) {
                    $this->recursiverender($this->page);
                }
                $this->page = $this->renderpage($this->page);
            }
            $this->templaterender($this->page);


            if ($canread) {
                $this->page->addaffcount();
                if ($this->user->isvisitor()) {
                    $this->page->addvisitcount();
                }

                // redirection using Location and 302
                if (!empty($this->page->redirection()) && $this->page->refresh() === 0 && $this->page->sleep() === 0) {
                    try {
                        if (Model::idcheck($this->page->redirection())) {
                            $this->routedirect('pageread/', ['page' => $this->page->redirection()]);
                        } else {
                            $url = getfirsturl($this->page->redirection());
                            $this->redirect($url);
                        }
                    } catch (RuntimeException $e) {
                        // TODO : send synthax error to editor
                    }
                }

                $html = file_get_contents($filedir);
                sleep($this->page->sleep());
                echo $html;
            }
            $this->pagemanager->update($this->page);
        }

        if (!$canread || !$pageexist) {
            http_response_code(404);
            $this->showtemplate(
                'alert',
                ['page' => $this->page, 'pageexist' => $pageexist, 'canedit' => $this->canedit()]
            );
        }
    }

    public function edit($page)
    {
        $this->setpage($page, 'pageedit');

        $this->pageconnect('pageedit');


        if ($this->importpage() && $this->canedit()) {
            $datas['tablist'] = [
                'main' => $this->page->main(),
                'css' => $this->page->css(),
                'header' => $this->page->header(),
                'nav' => $this->page->nav(),
                'aside' => $this->page->aside(),
                'footer' => $this->page->footer(),
                'body' => $this->page->body(),
                'javascript' => $this->page->javascript()
            ];

            $datas['faviconlist'] = $this->mediamanager->listfavicon();
            $datas['thumbnaillist'] = $this->mediamanager->listthumbnail();
            $datas['pagelist'] = $this->pagemanager->list();


            $pagelist = $this->pagemanager->pagelist();
            $datas['tagpagelist'] = $this->pagemanager->tagpagelist($this->page->tag('array'), $pagelist);
            $datas['lasteditedpagelist'] = $this->pagemanager->lasteditedpagelist(5, $pagelist);

            $datas['editorlist'] = $this->usermanager->getlisterbylevel(2, '>=');

            if (isset($_SESSION['workspace'])) {
                $datas['showleftpanel'] = $_SESSION['workspace']['showleftpanel'];
                $datas['showrightpanel'] = $_SESSION['workspace']['showrightpanel'];
            } else {
                $datas['showleftpanel'] = false;
                $datas['showrightpanel'] = false;
            }
            $datas = array_merge($datas, ['page' => $this->page, 'pageexist' => true, 'user' => $this->user]);
            $this->showtemplate('edit', $datas);
        } else {
            $this->routedirect('pageread/', ['page' => $this->page->id()]);
        }
    }

    public function log($page)
    {
        if ($this->user->issupereditor()) {
            $this->setpage($page, 'pagelog');
            $this->importpage();
            var_dump($this->page);
        } else {
            $this->routedirect('pageread/', ['page' => $page]);
        }
    }

    public function add($page)
    {
        $this->setpage($page, 'pageadd');

        $this->pageconnect('pageadd');

        if ($this->user->iseditor() && !$this->importpage()) {
            $this->page->reset();
            $this->page->addauthor($this->user->id());
            $this->pagemanager->add($this->page);
            $this->routedirect('pageedit', ['page' => $this->page->id()]);
        } else {
            $this->routedirect('pageread/', ['page' => $this->page->id()]);
        }
    }

    public function addascopy(string $page, string $copy)
    {
        $page = Model::idclean($page);
        if ($this->copy($copy, $page)) {
            $this->routedirect('pageedit', ['page' => $this->page->id()]);
        } else {
            $this->routedirect('pageread/', ['page' => $page]);
        }
    }

    public function confirmdelete($page)
    {
        $this->setpage($page, 'pageconfirmdelete');
        if ($this->importpage() && ($this->user->issupereditor() || $this->page->authors() === [$this->user->id()] )) {
            $this->showtemplate('confirmdelete', ['page' => $this->page, 'pageexist' => true]);
        } else {
            $this->routedirect('pageread/', ['page' => $this->page->id()]);
        }
    }

    public function download($page)
    {
        if ($this->user->isadmin()) {
            $file = Model::PAGES_DIR . Config::pagetable() . DIRECTORY_SEPARATOR . $page . '.json';

            if (file_exists($file)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/json; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . basename($file) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                readfile($file);
                exit;
            }
        } else {
            $this->routedirect('pageread/', ['page' => $page]);
        }
    }

    /**
     * Import page and save it into the database
     */
    public function upload()
    {
        $page = $this->pagemanager->getfromfile();

        if ($page !== false) {
            if (!empty($_POST['id'])) {
                $page->setid(Model::idclean($_POST['id']));
            }

            if ($_POST['datecreation']) {
                $page->setdatecreation($this->now);
            }

            if ($_POST['author']) {
                $page->setauthors([$this->user->id()]);
            }

            $page->setdaterender($page->datecreation('date'));

            if ($_POST['erase'] || $this->pagemanager->get($page) === false) {
                if ($this->pagemanager->add($page)) {
                    Model::sendflashmessage('Page successfully uploaded', 'success');
                }
            } else {
                Model::sendflashmessage('Page ID already exist, check remplace if you want to erase it', 'warning');
            }
        } else {
            Model::sendflashmessage('Error while importing page JSON', 'error');
        }
        $this->routedirect('home');
    }

    public function logout(string $page)
    {
        if (!$this->user->isvisitor()) {
            $this->disconnect();
            $this->routedirect('pageread', ['page' => $page]);
        } else {
            $this->routedirect('pageread/', ['page' => $page]);
        }
    }

    public function login(string $page)
    {
        if ($this->user->isvisitor()) {
            $this->showtemplate('connect', ['id' => $page, 'route' => 'pageread/']);
        } else {
            $this->routedirect('pageread/', ['page' => $page]);
        }
    }

    public function delete($page)
    {
        $this->setpage($page, 'pagedelete');
        if ($this->user->iseditor() && $this->importpage()) {
            $this->pagemanager->delete($this->page);
        }
        $this->routedirect('home');
    }

    public function duplicate(string $page, string $target)
    {
        $target = Model::idclean($target);
        if ($this->copy($page, $target)) {
            $this->routedirect('pageread/', ['page' => $target]);
        } else {
            $this->routedirect('pageread/', ['page' => Model::idclean($page)]);
        }
    }

    /**
     * Copy a page to a new ID
     *
     * @param string $srcid Source page ID
     * @param string $targetid Target page ID
     */
    public function copy(string $srcid, string $targetid)
    {
        if ($this->user->iseditor()) {
            $this->page = $this->pagemanager->get($srcid);
            if ($this->page !== false && $this->canedit() && $this->pagemanager->get($targetid) === false) {
                $this->page->setid($targetid);
                $this->page->setdatecreation(true); // Reset date of creation
                $this->page->setdatemodif(new DateTimeImmutable());
                $this->page->setdaterender(new DateTimeImmutable());
                $this->page->addauthor($this->user->id());
                $this->pagemanager->add($this->page);
                return true;
            }
        }
        return false;
    }

    public function update($page)
    {
        $this->setpage($page, 'pageupdate');

        $this->movepanels();
        $this->fontsize();



        if ($this->importpage()) {
            if ($this->canedit()) {
            // Check if someone esle edited the page during the editing.
                $oldpage = clone $this->page;
                $this->page->hydrate($_POST);

                $this->page->updateedited();
                $this->page->addauthor($this->user->id());
                $this->page->removeeditby($this->user->id());

                $this->pagemanager->update($this->page);


            //$this->showtemplate('updatemerge', $compare);
            } else {
                // If the editor session finished during the editing, let's try to reconnect to save the editing
                $_SESSION['pageupdate'] = $_POST;
                $_SESSION['pageupdate']['id'] = $this->page->id();
                $this->routedirect('connect');
            }
        }
        $this->routedirect('pageedit', ['page' => $this->page->id()]);
    }

    public function movepanels()
    {
        $_SESSION['workspace']['showrightpanel'] = isset($_POST['workspace']['showrightpanel']);
        $_SESSION['workspace']['showleftpanel'] = isset($_POST['workspace']['showleftpanel']);
    }

    public function fontsize()
    {
        if (!empty($_POST['fontsize']) && $_POST['fontsize'] !== Config::fontsize()) {
            Config::setfontsize($_POST['fontsize']);
            Config::savejson();
        }
    }

    public function pagedirect($page)
    {
        $this->routedirect('pageread/', ['page' => Model::idclean($page)]);
    }
}
