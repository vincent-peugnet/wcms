<?php

namespace Wcms;

use AltoRouter;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use RuntimeException;
use Wcms\Exception\Databaseexception;
use Wcms\Exception\Filesystemexception;

class Controllerpage extends Controller
{
    protected Page $page;
    protected Modelmedia $mediamanager;

    public function __construct(AltoRouter $router)
    {
        parent::__construct($router);

        $this->mediamanager = new Modelmedia();
    }

    /**
     * Check a given page ID. If it's not a valid ID, then redirect to the clean ID version.
     * If it's valid, setup $this->page` with a new empty Page using given ID.
     *
     * @param $id                           The id received by the router
     * @param $route                        The route that was used
     */
    protected function setpage(string $id, string $route): void
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
     *
     * @todo maybe throw error instead of logging to let the upper code choose what to do
     */
    protected function importpage(): bool
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
            Logger::warningex($e);
            return false;
        }
    }

    /**
     * show credentials for unconnected editors for a specific page
     *
     * @param string $route direction to redirect after the connection form
     * @return void
     */
    protected function pageconnect(string $route): void
    {
        if ($this->user->isvisitor()) {
            http_response_code(401);
            $this->showtemplate('connect', ['route' => $route, 'id' => $this->page->id()]);
            exit;
        }
    }

    public function render(string $page): void
    {
        $this->setpage($page, 'pageupdate');

        if ($this->importpage() && $this->user->iseditor()) {
            try {
                $urlchecker = Config::urlchecker() ? new Serviceurlchecker(6) : null;
                $this->page = $this->pagemanager->renderpage($this->page, $this->router, $urlchecker);
                $this->pagemanager->update($this->page);
                $this->pagemanager->templaterender($this->page, $this->router);
            } catch (RuntimeException $e) {
                $msg = $e->getMessage();
                Logger::error("Error while trying to render '$page': $msg");
            }
        }
        http_response_code(307);
        $this->routedirect('pageread', ['page' => $this->page->id()]);
    }

    /**
     * Delete render cache of all related pages
     *
     * @param string[] $relatedpages        List of page ids
     */
    protected function deletelinktocache(array $relatedpages): void
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
     * Minimal response to HEAD request.
     * Return either 200, 308 or 404 response codes.
     * Password protected, private and not_published Pages are considered as 200 OK
     */
    public function readhead(string $page): void
    {
        $this->setpage($page, 'pageread');
        if ($this->importpage()) {
            http_response_code(200);
        } else {
            http_response_code(404);
        }
    }

    /**
     * When a client want to display a page
     * Match domain.com/PAGE_ID
     * May send HTML, or redirect, or just 304 headers
     *
     * @param string $page page ID
     */
    public function read(string $page): void
    {
        $this->setpage($page, 'pageread');

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

        try {
            if ($this->pagemanager->needtoberendered($this->page)) {
                if (Config::deletelinktocache()) {
                    $oldlinkto = $this->page->linkto();
                }
                $urlchecker = Config::urlchecker() ? new Serviceurlchecker(3) : null;
                $this->page = $this->pagemanager->renderpage($this->page, $this->router, $urlchecker);
                if (isset($oldlinkto)) {
                    $relatedpages = array_unique(array_merge($oldlinkto, $this->page->linkto()));
                    $this->deletelinktocache($relatedpages);
                }
            }

            $this->pagemanager->templaterender($this->page, $this->router);

            $this->page->adddisplaycount();
            if ($this->user->isvisitor()) {
                $this->page->addvisitcount();
            }
            $this->pagemanager->update($this->page);
        } catch (RuntimeException $e) {
            Logger::error("Error while trying to read page '$page':" . $e->getMessage());
        }


        // redirection using Location and 302
        // If redirecting to a dead page or an invalid URL, redirection is canceled
        if (!empty($this->page->redirection()) && $this->page->refresh() === 0 && $this->page->sleep() === 0) {
            try {
                if (Model::idcheck($this->page->redirection())) {
                    $this->routedirect('pageread', ['page' => $this->page->redirection()]);
                } else {
                    $url = getfirsturl($this->page->redirection());
                    $this->redirect($url);
                }
            } catch (RuntimeException $e) {
                Logger::warning("Page '$page' redirect to existing page or a valid URL");
            }
        }

        // read rendered HTML and apply post process render if necessary
        $html = file_get_contents(Model::HTML_RENDER_DIR . $page . '.html');
        $postprocessor = new Servicepostprocess($this->page, $this->user);
        $html = $postprocessor->process($html);

        // Do some sleep if the page need to
        sleep($this->page->sleep());

        // Check if page can be cached by the Web browser
        if ($this->page->canbecached()) {
            // Remove HTTP headers added by PHP session_start()
            header_remove("Expires");
            header_remove("Cache-Control");
            header_remove("Pragma");

            // Activate cache strategy and send 'Last modified' which corrspond to page render date.
            $daterender = $this->page->daterender();
            assert($daterender instanceof DateTimeImmutable);
            $lastmodified = date_format($daterender->setTimezone(new DateTimeZone('GMT')), DATE_RFC7231);
            header('Cache-Control: max-age=0, must-revalidate');
            header("Last-Modified: $lastmodified");

            // Check if the client already have a version of the page
            if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
                $ifmodifiedsince = DateTime::createFromFormat(
                    DATE_RFC7231,
                    $_SERVER['HTTP_IF_MODIFIED_SINCE'],
                    new DateTimeZone('GMT')
                );

                // If the version is still valid (not modified since download), we can send a 304 response and exit.
                if ($ifmodifiedsince >= $this->page->daterender()) {
                    http_response_code(304);
                    exit;
                }
            }
        }

        // If the page cannot be cached, or the client don't have an old version of the page, we send the HTML
        http_response_code(200);
        echo $html;
    }

    public function edit(string $page): void
    {
        $this->setpage($page, 'pageedit');

        $this->pageconnect('pageedit');

        if ($this->importpage()) {
            if (!$this->canedit($this->page)) {
                http_response_code(403);
                $this->showtemplate('forbidden', ['route' => 'pageedit', 'id' => $this->page->id()]);
                exit;
            }

            $servicetags = new Servicetags();

            try {
                $datas['taglist'] = $servicetags->taglist();
            } catch (Filesystemexception $e) {
                Logger::errorex($e);
            }

            $datas['faviconlist'] = $this->mediamanager->listfavicon();
            $datas['thumbnaillist'] = $this->mediamanager->listthumbnail();
            $datas['pagelist'] = $this->pagemanager->list();
            $datas['target'] = hash('crc32', $this->page->id() . rand(0, 2048));
            $datas['editorlist'] = $this->usermanager->getlisterbylevel(2, '>=', true);
            $datas['page'] = $this->page;

            $backlinkopt = new Opt(['linkto' => $this->page->id()]);
            $datas['homebacklink'] = $backlinkopt->getaddress();

            $datas['urls'] = [];
            if (count($this->page->externallinks()) > 0) {
                $urlchecker = new Serviceurlchecker();
                foreach ($this->page->externallinks() as $url => $status) {
                    try {
                        $datas['urls'][$url] = $urlchecker->info($url);
                    } catch (RuntimeException $e) {
                        // No cached infos about this URL
                    }
                }
            }

            $this->showtemplate('edit', $datas);
        } else {
            $this->routedirect('pageread', ['page' => $this->page->id()]);
        }
    }

    /**
     * Print page's datas. Used for debug. Kind of obscure nowdays.
     */
    public function log(string $page): void
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

    /**
     * When a client want to add a page.
     * Match domain.com/PAGE_ID/add
     *
     * @throws RuntimeException if page creation failed
     */
    public function add(string $page): void
    {
        $this->setpage($page, 'pageadd');

        $this->pageconnect('pageadd');

        if (!$this->user->iseditor()) {
            http_response_code(403);
            $this->showtemplate('forbidden', ['route' => 'pageedit', 'id' => $this->page->id()]);
            exit;
        }

        if ($this->importpage()) {
            http_response_code(403);
            $message = 'page already exist with this ID';
            $this->showtemplate('forbidden', ['route' => 'pageedit', 'id' => $this->page->id(), 'message' => $message]);
            exit;
        }

        $this->page->reset();
        if (isset($_SESSION['dirtyid'])) {
            $this->page->settitle($_SESSION['dirtyid'][$page]);
            unset($_SESSION['dirtyid']);
        }
        $this->page->addauthor($this->user->id());
        $this->pagemanager->add($this->page);
        $this->routedirect('pageedit', ['page' => $this->page->id()]);
    }

    public function addascopy(string $page, string $copy): void
    {
        $page = Model::idclean($page);
        if ($this->copy($copy, $page)) {
            $this->routedirect('pageedit', ['page' => $this->page->id()]);
        } else {
            $this->routedirect('pageread', ['page' => $page]);
        }
    }

    public function download(string $page): void
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
    public function upload(): void
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
                try {
                    $this->pagemanager->add($page);
                    $this->sendflashmessage('Page successfully uploaded', self::FLASH_SUCCESS);
                } catch (RuntimeException $e) {
                    $this->sendflashmessage($e->getMessage(), self::FLASH_ERROR);
                    Logger::errorex($e);
                }
            } else {
                $this->sendflashmessage(
                    'Page ID already exist, check remplace if you want to erase it',
                    self::FLASH_WARNING
                );
            }
        } else {
            $this->sendflashmessage('Error while importing page JSON', self::FLASH_ERROR);
        }
        $this->routedirect('home');
    }

    public function logout(string $page): void
    {
        if (!$this->user->isvisitor()) {
            $this->disconnect();
            $this->routedirect('pageread', ['page' => $page]);
        } else {
            $this->routedirect('pageread', ['page' => $page]);
        }
    }

    public function login(string $page): void
    {
        if ($this->user->isvisitor()) {
            $this->showtemplate('connect', ['id' => $page, 'route' => 'pageread']);
        } else {
            $this->routedirect('pageread', ['page' => $page]);
        }
    }

    public function delete(string $page): void
    {
        $this->setpage($page, 'pagecdelete');
        if ($this->importpage() && $this->candelete($this->page)) {
            $linksto = new Opt();
            $linksto->setlinkto($this->page->id());
            $pageslinkingto = $this->pagemanager->pagetable($this->pagemanager->pagelist(), $linksto);
            $cancelroute = isset($_GET['route']) ? $_GET['route'] : 'pageread';
            $this->showtemplate('delete', [
                'page' => $this->page,
                'pageexist' => true,
                'pageslinkingtocount' => count($pageslinkingto),
                'cancelroute' => $cancelroute,
            ]);
        } else {
            $this->routedirect('pageread', ['page' => $this->page->id()]);
        }
    }

    /**
     * @todo maybe show an error view if deletion failed
     */
    public function confirmdelete(string $page): void
    {
        $this->setpage($page, 'pageconfirmdelete');
        if (!$this->importpage() || !$this->candelete($this->page)) {
            http_response_code(403);
            $this->showtemplate('forbidden', ['route' => 'pageread', 'id' => $this->page->id()]);
            exit;
        }

        try {
            $this->pagemanager->delete($this->page);
            $user = $this->user->id();
            Logger::info("User '$user' uccessfully deleted Page '$page'");
        } catch (Filesystemexception $e) {
            Logger::warning("Error while deleting Page '$page'" . $e->getMessage());
        } catch (Databaseexception $e) {
            Logger::error("Could not delete Page $page: " . $e->getMessage());
        }
        $this->routedirect('pageread', ['page' => $this->page->id()]);
    }

    public function duplicate(string $page, string $duplicate): void
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
    protected function copy(string $srcid, string $targetid): bool
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

    public function update(string $page): void
    {
        $this->setpage($page, 'pageupdate');


        if ($this->importpage()) {
            if ($this->canedit($this->page)) {
                // Check if someone esle edited the page during the editing.
                $oldpage = clone $this->page;
                $this->page->hydrate($_POST);

                if ($oldpage->datemodif() != $this->page->datemodif()) {
                    $this->sendflashmessage("Page has been modified by someone else", self::FLASH_WARNING);
                    $_SESSION['pageupdate'] = $_POST;
                    $_SESSION['pageupdate']['id'] = $this->page->id();
                    $this->routedirect('pageedit', ['page' => $this->page->id()]);
                } else {
                    try {
                        $this->page->updateedited();
                        $this->pagemanager->update($this->page);
                        $this->sendflashmessage('Page succesfully updated', self::FLASH_SUCCESS);
                    } catch (RuntimeException $e) {
                        $this->sendflashmessage('Error while trying to update', self::FLASH_ERROR);
                        Logger::error("Error while trying to update Page '$page': " . $e->getMessage());
                    }
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
     * Permanent redirection to a page.
     * Send a `301` HTTP code.
     *
     * Used only with Route `pageread/` redirecting to `pageread`
     */
    public function pagepermanentredirect(string $page): void
    {
        $path = $this->generate('pageread', ['page' => Model::idclean($page)]);
        header("Location: $path", true, 301);
    }

    /**
     * Send a `404` HTTP code and display 'Command not found'
     */
    public function commandnotfound(string $page, string $command): void
    {
        http_response_code(404);
        $this->showtemplate('alertcommandnotfound', ['command' => $command, 'id' => strip_tags($page)]);
    }
}
