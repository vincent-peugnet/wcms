<?php

namespace Wcms;

use RuntimeException;

class Controllerrandom extends Controller
{
    protected ?Optrandom $optrandom;

    public function direct()
    {
        $this->optrandom = new Optrandom($_GET);

        try {
            $origin = $this->pagemanager->get($this->optrandom->origin());

            $pages = $this->pagemanager->pagelist();
            $pages = $this->pagemanager->pagetable($pages, $this->optrandom);
            unset($pages[$origin->id()]);
            if (!empty($pages)) {
                $page = $pages[array_rand($pages)];

                if (in_array($page->id(), $origin->linkto())) {
                    $this->routedirect('pageread', ['page' => $page->id()]);
                } else {
                    $message = 'Wrong filters';
                }
            } else {
                $message = 'Empty set of page';
            }
        } catch (RuntimeException $e) {
            $message = 'Origin page does not exist';
        }

        if (isset($message)) {
            $this->showtemplate('alertrandom', ['message' => $message]);
        }
    }
}
