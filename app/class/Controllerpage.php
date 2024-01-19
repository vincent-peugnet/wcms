<?php

namespace Wcms;

use DateTimeImmutable;
use RuntimeException;

class Controllerpage extends Controller
{
    /** @var Page */
    protected $page;
    protected $mediamanager;

    public function __construct($router)
    {
        parent::__construct($router);

        $this->mediamanager = new Modelmedia();
    }

    public function setpage(string $id, string $route)
    {
        $cleanid = Model::idclean($id);
        if ($cleanid !== $id) {
            if ($route === 'pageadd') {
                $_SESSION['dirtyid'][$cleanid] = rawurldecode($id);
            }
            http_response_code(308);
            $this->routedirect($route, ['page' => $cleanid]);
        } else {
            $this->page = $this->pagemanager->newpage(['id' => $cleanid]);
        }
    }

    /**
     * Try yo import by multiple way the called page
     *
     * @return bool                         If a page is found and stored in `$this->page`
     */
    public function importpage(): bool
    {
        try {
            if (isset($_SESSION['pageupdate']['id']) && $_SESSION['pageupdate']['id'] == $this->page->id()) {
                $this->page = $this->pagemanager->parsepage($_SESSION['pageupdate']);
                unset($_SESSION['pageupdate']);
                return true;
            } else {
                $this->page = $this->pagemanager->get($this->page);
                return true;
            }
        } catch (RuntimeException $e) {
            Logger::errorex($e);
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
            http_response_code(401);
            $this->showtemplate('connect', ['route' => $route, 'id' => $this->page->id()]);
            exit;
        }
    }

    public function render($page)
    {
        $this->setpage($page, 'pageupdate');

        if ($this->importpage() && $this->user->iseditor()) {
            $this->page = $this->pagemanager->renderpage($this->page, $this->router);
            $this->pagemanager->update($this->page);
            $this->templaterender($this->page);
        }
        http_response_code(307);
        $this->routedirect('pageread', ['page' => $this->page->id()]);
    }

    /**
     * Delete render cache of all related pages
     *
     * @param string[] $relatedpages        List of page ids
     */
    public function deletelinktocache(array $relatedpages): void
    {
        foreach ($relatedpages as $pageid) {
            try {
                $this->pagemanager->unlink($pageid);
            } catch (RuntimeException $e) {
                Logger::errorex($e, true);
            }
        }
    }

    /**
     * Render page's JS and CSS templates if they need to
     *
     * @param Page $page page to check templates
     *
     * @todo Move this function in Modelpage
     */
    private function templaterender(Page $page)
    {
        try {
            $templates = $this->pagemanager->getpagecsstemplates($page);
            foreach ($templates as $page) {
                if ($this->pagemanager->needtoberendered($page)) {
                    $page = $this->pagemanager->renderpage($page, $this->router);
                    $this->pagemanager->update($page);
                }
            }
        } catch (RuntimeException $e) {
            Logger::errorex($e);
        }
        if (!empty($page->templatejavascript())) {
            try {
                $templatejs = $this->pagemanager->get($page->templatejavascript());
                if ($this->pagemanager->needtoberendered($templatejs)) {
                    $templatejs = $this->pagemanager->renderpage($templatejs, $this->router);
                    $this->pagemanager->update($templatejs);
                }
            } catch (RuntimeException $e) {
                Logger::errorex($e, true);
            }
        }
    }

    /**
     * @param string $page page ID
     */
    public function read($page)
    {
        $this->setpage($page, 'pageread');
        $filedir = Model::HTML_RENDER_DIR . $page . '.html';
        $reccursiverender = false;

        if (!$this->importpage()) {
            http_response_code(404);
            $this->showtemplate(
                'alertexistnot',
                ['page' => $this->page, 'subtitle' => Config::existnot()]
            );
            exit;
        }

        // Check page password
        if (!empty($this->page->password())) {
            if (empty($_POST['pagepassword']) || $_POST['pagepassword'] !== $this->page->password()) {
                $this->showtemplate('pagepassword', ['pageid' => $this->page->id()]);
                exit;
            }
        }

        if ($this->user->level() < $this->page->secure()) {
            http_response_code(403);
            switch ($this->page->secure()) {
                case Page::NOT_PUBLISHED:
                    $this->showtemplate(
                        'alertnotpublished',
                        ['page' => $this->page, 'subtitle' => Config::notpublished()]
                    );
                    break;

                case Page::PRIVATE:
                    $this->showtemplate(
                        'alertprivate',
                        ['page' => $this->page, 'subtitle' => Config::private()]
                    );
                    break;
            }
            exit;
        }

        if ($this->pagemanager->needtoberendered($this->page)) {
            if (Config::deletelinktocache() && $this->page->daterender() <= $this->page->datemodif()) {
                $oldlinkto = $this->page->linkto();
            }
            $this->page = $this->pagemanager->renderpage($this->page, $this->router);
            if (isset($oldlinkto)) {
                $relatedpages = array_unique(array_merge($oldlinkto, $this->page->linkto()));
                $this->deletelinktocache($relatedpages);
            }
        }

        $this->templaterender($this->page);


        $this->page->adddisplaycount();
        if ($this->user->isvisitor()) {
            $this->page->addvisitcount();
        }

        // redirection using Location and 302
        if (!empty($this->page->redirection()) && $this->page->refresh() === 0 && $this->page->sleep() === 0) {
            try {
                if (Model::idcheck($this->page->redirection())) {
                    $this->routedirect('pageread', ['page' => $this->page->redirection()]);
                } else {
                    $url = getfirsturl($this->page->redirection());
                    $this->redirect($url);
                }
            } catch (RuntimeException $e) {
                // TODO : send synthax error to editor
            }
        }
        $html = file_get_contents($filedir);

        $postprocessor = new Servicepostprocess($this->page, $this->user);
        $html = $postprocessor->process($html);

        sleep($this->page->sleep());

        echo $html;

        $this->pagemanager->update($this->page);
    }

    public function edit($page)
    {
        $this->setpage($page, 'pageedit');

        $this->pageconnect('pageedit');

        if ($this->importpage()) {
            if (!$this->canedit($this->page)) {
                http_response_code(403);
                $this->showtemplate('forbidden', ['route' => 'pageedit', 'id' => $this->page->id()]);
                exit;
            }

            $datas['faviconlist'] = $this->mediamanager->listfavicon();
            $datas['thumbnaillist'] = $this->mediamanager->listthumbnail();
            $datas['pagelist'] = $this->pagemanager->list();
            $datas['target'] = hash('crc32', $this->page->id() . rand(0, 2048));
            $datas['editorlist'] = $this->usermanager->getlisterbylevel(2, '>=', true);
            $datas['page'] = $this->page;

            $this->showtemplate('edit', $datas);
        } else {
            $this->routedirect('pageread', ['page' => $this->page->id()]);
        }
    }

    public function log($page)
    {
        if ($this->user->issupereditor()) {
            $this->setpage($page, 'pagelog');
            $this->importpage();
            echo '<pre>';
            var_dump($this->page);
            echo '</pre>';
        } else {
            $this->routedirect('pageread', ['page' => $page]);
        }
    }

    public function add($page)
    {
        $this->setpage($page, 'pageadd');

        $this->pageconnect('pageadd');

        if ($this->user->iseditor() && !$this->importpage()) {
            $this->page->reset();
            if (isset($_SESSION['dirtyid'])) {
                $this->page->settitle($_SESSION['dirtyid'][$page]);
                unset($_SESSION['dirtyid']);
            }
            $this->page->addauthor($this->user->id());
            $this->pagemanager->add($this->page);
            $this->routedirect('pageedit', ['page' => $this->page->id()]);
        } else {
            http_response_code(403);
            $this->showtemplate('forbidden', ['route' => 'pageedit', 'id' => $this->page->id()]);
        }
    }

    public function addascopy(string $page, string $copy)
    {
        $page = Model::idclean($page);
        if ($this->copy($copy, $page)) {
            $this->routedirect('pageedit', ['page' => $this->page->id()]);
        } else {
            $this->routedirect('pageread', ['page' => $page]);
        }
    }

    public function download($page)
    {
        $this->setpage($page, 'pagedownload');

        if (!$this->importpage()) {
            $this->routedirect('pageread', ['page' => $page]);
        }

        $this->pageconnect('pagedownload');

        if (!$this->canedit($this->page)) {
            http_response_code(403);
            $this->showtemplate('forbidden', ['id' => $this->page->id(), 'route' => 'pagedownload']);
            exit;
        }

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

            if ($_POST['erase'] || !$this->pagemanager->exist($page)) {
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
            $this->routedirect('pageread', ['page' => $page]);
        }
    }

    public function login(string $page)
    {
        if ($this->user->isvisitor()) {
            $this->showtemplate('connect', ['id' => $page, 'route' => 'pageread']);
        } else {
            $this->routedirect('pageread', ['page' => $page]);
        }
    }

    public function delete($page)
    {
        $this->setpage($page, 'pagecdelete');
        if ($this->importpage() && $this->candelete($this->page)) {
            $linksto = new Opt();
            $linksto->setlinkto($this->page->id());
            $pageslinkingto = $this->pagemanager->pagetable($this->pagemanager->pagelist(), $linksto);
            $this->showtemplate('delete', [
                'page' => $this->page,
                'pageexist' => true,
                'pageslinkingtocount' => count($pageslinkingto),
            ]);
        } else {
            $this->routedirect('pageread', ['page' => $this->page->id()]);
        }
    }

    public function confirmdelete($page)
    {
        $this->setpage($page, 'pageconfirmdelete');
        if ($this->user->iseditor() && $this->importpage()) {
            $this->pagemanager->delete($this->page);
            $this->routedirect('pageread', ['page' => $this->page->id()]);
        } else {
            http_response_code(403);
            $this->showtemplate('forbidden', ['route' => 'pageread', 'id' => $this->page->id()]);
        }
    }

    public function duplicate(string $page, string $duplicate)
    {
        $duplicate = Model::idclean($duplicate);
        if ($this->copy($page, $duplicate)) {
            $this->routedirect('pageread', ['page' => $duplicate]);
        } else {
            $this->routedirect('pageread', ['page' => Model::idclean($page)]);
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
            try {
                $this->page = $this->pagemanager->get($srcid);
                if ($this->canedit($this->page) && !$this->pagemanager->exist($targetid)) {
                    $this->page->setid($targetid);
                    $this->page->setdatecreation(true); // Reset date of creation
                    $this->page->setdatemodif(new DateTimeImmutable());
                    $this->page->setdaterender(new DateTimeImmutable());
                    $this->page->addauthor($this->user->id());
                    $this->pagemanager->add($this->page);
                    return true;
                }
            } catch (RuntimeException $e) {
                Logger::errorex($e, true);
            }
        }
        return false;
    }

    public function update($page)
    {
        $this->setpage($page, 'pageupdate');


        if ($this->importpage()) {
            if ($this->canedit($this->page)) {
                // Check if someone esle edited the page during the editing.
                $oldpage = clone $this->page;
                $this->page->hydrate($_POST);

                if ($oldpage->datemodif() != $this->page->datemodif()) {
                    Model::sendflashmessage("Page has been modified by someone else", Model::FLASH_WARNING);
                    $_SESSION['pageupdate'] = $_POST;
                    $_SESSION['pageupdate']['id'] = $this->page->id();
                    $this->routedirect('pageedit', ['page' => $this->page->id()]);
                } else {
                    $this->page->updateedited();

                    $this->pagemanager->update($this->page);
                }
            } else {
                // If the editor session finished during the editing, let's try to reconnect to save the editing
                $_SESSION['pageupdate'] = $_POST;
                $_SESSION['pageupdate']['id'] = $this->page->id();
                $this->routedirect('connect');
            }
        }
        $this->routedirect('pageedit', ['page' => $this->page->id()]);
    }

    /**
     * Temporary redirection to a page.
     * Send a `302` HTTP code.
     */
    public function pagedirect($page): void
    {
        $this->routedirect('pageread', ['page' => Model::idclean($page)]);
    }

    /**
     * Permanent redirection to a page.
     * Send a `301` HTTP code.
     */
    public function pagepermanentredirect($page): void
    {
        $path = $this->generate('pageread', ['page' => Model::idclean($page)]);
        header("Location: $path", true, 301);
    }

    /**
     * Send a `404` HTTP code and display 'Command not found'
     *
     * @todo use a dedicated view and suggest a list of W URL based command.
     */
    public function commandnotfound($page, $command): void
    {
        http_response_code(404);
        $this->showtemplate('alertcommandnotfound', ['command' => $command, 'id' => strip_tags($page)]);
    }
}
