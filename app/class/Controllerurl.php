<?php

namespace Wcms;

use AltoRouter;
use RuntimeException;
use Wcms\Exception\Filesystemexception\Notfoundexception;

class Controllerurl extends Controller
{
    protected Serviceurlchecker $urlmanager;


    public function __construct(AltoRouter $router)
    {
        parent::__construct($router);

        if ($this->user->isvisitor()) {
            $this->showtemplate('connect', ['route' => 'url']);
        }
        if (!$this->user->issupereditor()) {
            $this->showtemplate('forbidden', [], 403);
        }
        $this->urlmanager = new Serviceurlchecker();
    }

    public function desktop(): void
    {
        $sortby = $_GET['sortby'] ?? 'timestamp';
        $order = $_GET['order'] ?? 1;
        $urls = $this->urlmanager->list($sortby, $order);
        $urls = array_reverse($urls);
        $this->showtemplate('url', [
            'urls' => $urls,
            'sortby' => $sortby,
            'reverseorder' => $order * -1,
            'total' => count($urls),
        ]);
    }

    public function edit(): void
    {
        $this->urlmanager->timeout = 6;
        $ids = $_POST['id'] ?? [];

        if (empty($ids)) {
            $this->sendflashmessage('no selected URL', self::FLASH_WARNING);
            $this->routedirect('url');
        }

        foreach ($ids as $id) {
            if (!$this->urlmanager->iscached($id)) {
                continue;
            }
            $this->urlmanager->addtoqueue($id);
        }
        try {
            $count = $this->urlmanager->processqueue();
            $this->urlmanager->savecache();
            $this->sendflashmessage("$count URL(s) were processed", self::FLASH_SUCCESS);
        } catch (RuntimeException $e) {
            Logger::errorex($e);
            $this->sendflashmessage('an error occured: ' . $e->getMessage(), self::FLASH_ERROR);
        }

        $this->routedirect('url');
    }

    public function flushurlcache(): never
    {
        if (!$this->user->isadmin()) {
            $this->showtemplate('forbidden', [], 403);
        }
        try {
            Fs::deletefile(Model::URLS_FILE);
            $this->sendflashmessage('URL cache successfully deleted', self::FLASH_SUCCESS);
            $user = $this->user->id();
            Logger::info("URL cache successfully deleted by user '$user'");
        } catch (Notfoundexception $e) {
            $this->sendflashmessage('URL cache is already deleted ' . $e->getMessage(), self::FLASH_WARNING);
        } catch (RuntimeException $e) {
            $msg = 'Error while trying to flush page render cache:' . $e->getMessage();
            $this->sendflashmessage($msg, self::FLASH_ERROR);
            Logger::error($msg);
        }
        $this->routedirect('url');
    }

    /**
     * Remove unused URLs from cache
     */
    public function cleanurlcache(): never
    {
        if (!$this->user->issupereditor()) {
            $this->showtemplate('forbidden', [], 403);
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
        $this->routedirect('url');
    }
}
