<?php

namespace Wcms;

class Controllerapipage extends Controllerapi
{
    use Voterpage;

    /** @var Page|null $page */
    protected $page;

    /**
     * Check if page ID is valid and exist.
     * If so, it is stored in Controller $page property
     *
     * - Send `406` Error code in case of invalid ID
     * - Send `404` Error code if page is not found
     *
     * @var string $id Page ID to look for
     * @return bool in case of success or failure
     */
    public function importpage(string $id): bool
    {
        if (!Model::idcheck($id)) {
            http_response_code(406);
            return false;
        } else {
            $this->page = $this->pagemanager->get($id);
            if ($this->page === false) {
                http_response_code(404);
                return false;
            } else {
                return true;
            }
        }
    }

    public function access(string $page)
    {
        if ($this->importpage($page)) {
            if ($this->canedit()) {
                http_response_code(200);
                header('Content-type: application/json; charset=utf-8');
                echo json_encode($this->page->dry(), JSON_PRETTY_PRINT);
            } else {
                http_response_code(401);
            }
        }
    }

    public function update(string $page)
    {
        if ($this->importpage($page)) {
            if ($this->canedit()) {
                $oldpage = clone $this->page;
                $this->page->hydrate($_POST);

                if ($this->page->datemodif() == $oldpage->datemodif()) {
                    $this->page->updateedited();
                    $this->page->addauthor($this->user->id());
                    if ($this->pagemanager->update($this->page)) {
                        http_response_code(200);
                        header('Content-type: application/json; charset=utf-8');
                        echo json_encode($this->page->dry(), JSON_PRETTY_PRINT);
                    } else {
                        http_response_code(500);
                    }
                } else {
                    http_response_code(409);
                }
            }
        }
    }
}
