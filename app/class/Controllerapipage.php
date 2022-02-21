<?php

namespace Wcms;

class Controllerapipage extends Controllerapi
{
    use Voterpage;

    /** @var Page|null $page */
    protected $page;

    public function access($page)
    {
        if (!Model::idcheck($page)) {
            http_response_code(406);
            exit;
        } else {
            $this->page = $this->pagemanager->get($page);
            if ($this->page === false) {
                http_response_code(404);
                exit;
            } elseif (!$this->canedit()) {
                http_response_code(401);
                exit;
            } else {
                http_response_code(200);
                header('Content-type: application/json; charset=utf-8');
                echo json_encode($this->page->dry(), JSON_PRETTY_PRINT);
                exit;
            }
        }
    }
}
