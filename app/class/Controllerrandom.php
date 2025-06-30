<?php

namespace Wcms;

use RuntimeException;

class Controllerrandom extends Controller
{
    protected ?Optrandom $optrandom;

    public function direct(): never
    {
        $this->optrandom = new Optrandom($_GET);

        try {
            $origin = $this->pagemanager->get($this->optrandom->origin());

            $pages = $this->pagemanager->pagelist();
            $pages = $this->pagemanager->pagetable($pages, $this->optrandom);
            unset($pages[$origin->id()]);
            $keys = array_intersect_key($pages, array_flip($origin->linkto()));
            if (!empty($keys)) {
                $page = $pages[array_rand($keys)];
                $this->routedirect('pageread', ['page' => $page->id()]);
            } else {
                $message = 'Empty set of page';
            }
        } catch (RuntimeException $e) {
            $message = 'Origin page does not exist';
        }
        $this->showtemplate('alertrandom', ['message' => $message]);
    }
}
