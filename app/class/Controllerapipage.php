<?php

namespace Wcms;

use JsonException;
use RuntimeException;

class Controllerapipage extends Controllerapi
{
    use Voterpage;

    /** @var Page|null $page */
    protected ?Page $page;

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
            try {
                $this->page = $this->pagemanager->get($id);
            } catch (RuntimeException $e) {
                http_response_code(404);
                return false;
            }
            return true;
        }
    }

    /**
     * - Send `401` if user can't edit page
     */
    public function get(string $page)
    {
        if ($this->importpage($page)) {
            if ($this->canedit($this->page)) {
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
            if ($this->canedit($this->page)) {
                if (!empty($_POST)) {
                    $datas = $_POST;
                } else {
                    $datas = $this->recievejson();
                    if (empty($datas)) {
                        $this->shortresponse(400, "No POST or JSON datas recieved");
                    }
                }
                $oldpage = clone $this->page;
                $update = new Page($datas);
                if (!is_null($update->id()) && $update->id() !== $this->page->id()) {
                    $this->shortresponse(400, "Page ID and datas ID doesn't match");
                }
                $this->page->hydrate($datas);

                if ($this->page->datemodif() == $oldpage->datemodif()) {
                    $this->page->updateedited();
                    if ($this->pagemanager->update($this->page)) {
                        http_response_code(200);
                        header('Content-type: application/json; charset=utf-8');
                        echo json_encode($this->page->dry(), JSON_PRETTY_PRINT);
                    } else {
                        $this->shortresponse(500, "Error while trying to save page in database");
                    }
                } else {
                    $this->shortresponse(409, "Conflict : A more recent version of the page is stored in the database");
                }
            } else {
                http_response_code(401);
                $this->shortresponse(401, "You are not alowed to edit this page");
            }
        }
    }

    public function add(string $page)
    {
        if (!Model::idcheck($page)) {
            $this->shortresponse(406, 'ID is not valid');
        }
        if (!$this->user->iseditor()) {
            $this->shortresponse(401, 'User cannot create pages');
        }
        if ($this->pagemanager->exist($page)) {
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

    public function put(string $page)
    {
        if (!Model::idcheck($page)) {
            $this->shortresponse(406, 'ID is not valid');
        }
        $exist = $this->importpage($page);
        if (!$exist && !$this->user->iseditor()) {
            $this->shortresponse(401, 'User cannot create pages');
        }
        if ($exist && !$this->canedit($this->page)) {
            $this->shortresponse(401, 'Page already exist but user cannot update it');
        }
        $this->page = new Page(array_merge($this->recievejson(), ['id' => $page]));
        if ($this->pagemanager->update($this->page)) {
            http_response_code($exist ? 200 : 201);
        } else {
            $this->shortresponse(500, "Error while trying to save page in database");
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

    public function list()
    {
        if (!$this->user->iseditor()) {
            http_response_code(401);
        }
        http_response_code(200);
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($this->pagemanager->list());
    }

    public function query()
    {
        if (!$this->user->iseditor()) {
            http_response_code(401);
        }
        $jsondatas = $this->recievejson();
        if (empty($jsondatas) && empty($_POST)) {
            $this->shortresponse(400, "No POST or JSON datas recieved");
        }
        $datas = empty($jsondatas) ? $_POST : $jsondatas;
        $opt = new Opt($datas);
        $pages = $this->pagemanager->pagelist();
        $pages = $this->pagemanager->pagetable($pages, $opt);
        $pages = array_map(function (Page $page) {
            return $page->dry();
        }, $pages);
        http_response_code(200);
        header('Content-type: application/json; charset=utf-8');
        echo json_encode(array_values($pages));
    }
}
