<?php

namespace Wcms;

use RuntimeException;

class Controllerurl extends Controller
{
    protected Serviceurlchecker $urlmanager;

    public function __construct($router)
    {
        parent::__construct($router);
        // Managing URLS is reserved to supereditors and above
        if (!$this->user->issupereditor()) {
            http_response_code(304);
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
