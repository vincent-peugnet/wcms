<?php

namespace Wcms;

use AltoRouter;
use RuntimeException;

class Controllerurl extends Controller
{
    protected Serviceurlchecker $urlmanager;


    public function __construct(AltoRouter $router)
    {
        parent::__construct($router);

        if ($this->user->isvisitor()) {
            http_response_code(401);
            $this->showtemplate('connect', ['route' => 'url']);
        }
        if (!$this->user->issupereditor()) {
            http_response_code(403);
            $this->showtemplate('forbidden');
        }
        $this->urlmanager = new Serviceurlchecker();
    }

    public function desktop(): void
    {
        $sortby = $_GET['sortby'] ?? 'id';
        $order = $_GET['order'] ?? 1;
        $urls = $this->urlmanager->list($sortby, $order);
        // ksort($urls);
        $urls = array_reverse($urls);
        $this->showtemplate('url', ['urls' => $urls]);
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
}
