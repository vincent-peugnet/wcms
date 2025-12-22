<?php

namespace Wcms;

use RuntimeException;

class Controllerurl extends Controller
{
    protected Serviceurlchecker $urlmanager;


    public function __construct($router)
    {
        parent::__construct($router);

        if ($this->user->isvisitor()) {
            http_response_code(401);
            $this->showtemplate('connect', ['route' => 'url']);
            exit;
        }
        if (!$this->user->issupereditor()) {
            http_response_code(403);
            $this->showtemplate('forbidden');
            exit;
        }
        $this->urlmanager = new Serviceurlchecker();
    }

    public function desktop()
    {
        $urls = $this->urlmanager->list();
        // ksort($urls);
        $urls = array_reverse($urls);
        $this->showtemplate('url', ['urls' => $urls]);
    }
}
