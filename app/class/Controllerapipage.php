<?php

namespace Wcms;

use JsonException;
use RuntimeException;

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
     * @param string $id Page ID to look for
     * @return bool in case of success or failure
     */
    protected function importpage(string $id): bool
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

    /**
     * - Send `401` if user can't edit page
     */
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

    /**
     * update a page
     * Check datas in request body first, then check POST datas
     *
     * - Send `400` if no datas are send
     * - Send `401` if user can't edit page
     * - Send `409` in case of conflict
     * - Send `500`
     */
    public function update(string $page)
    {
        if ($this->importpage($page)) {
            if ($this->canedit()) {
                $oldpage = clone $this->page;
                $json = $this->getrequestbody();
                var_export($json);
                if (!empty($json)) {
                    try {
                        $datas = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
                    } catch (JsonException $e) {
                        $this->shortresponse(400, "Json decoding error: " . $e->getMessage());
                    }
                } elseif (!empty($_POST)) {
                    $datas = $_POST;
                } else {
                    http_response_code(400);
                    exit;
                }
                $this->page->hydrate($datas);

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
            } else {
                http_response_code(401);
            }
        }
    }

    public function add(string $page)
    {
        if (!Model::idcheck($page)) {
            http_response_code(406);
            exit;
        }
        if (!$this->user->iseditor()) {
            http_response_code(401);
            exit;
        }
        if ($this->pagemanager->get($page)) {
            http_response_code(405);
            exit;
        }
        $this->page = new Page(["id" => $page]);
        $this->page->reset();
        $this->page->addauthor($this->user->id());
        if ($this->pagemanager->add($this->page)) {
            http_response_code(200);
        } else {
            http_response_code(500);
        }
    }

    public function delete(string $page)
    {
        if ($this->importpage($page)) {
            if ($this->user->issupereditor() || $this->page->authors() === [$this->user->id()]) {
                if ($this->pagemanager->delete($this->page)) {
                    http_response_code(200);
                } else {
                    http_response_code(500);
                }
            } else {
                http_response_code(401);
            }
        }
    }
}
