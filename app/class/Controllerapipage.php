<?php

namespace Wcms;

use RuntimeException;
use Wcms\Exception\Databaseexception;
use Wcms\Exception\Filesystemexception;

class Controllerapipage extends Controllerapi
{
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
    public function get(string $page): void
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
    public function update(string $page): void
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
                if (isset($datas['id']) && $datas['id'] !== $this->page->id()) {
                    $this->shortresponse(400, "Page ID and datas ID doesn't match");
                }
                $this->page->hydrate($datas);

                if ($this->page->datemodif() == $oldpage->datemodif()) {
                    try {
                        $this->page->updateedited();
                        $this->pagemanager->update($this->page);
                        http_response_code(200);
                        header('Content-type: application/json; charset=utf-8');
                        echo json_encode($this->page->dry(), JSON_PRETTY_PRINT);
                    } catch (RuntimeException $e) {
                        Logger::error("Error while trying to update Page '$page' through API: " . $e->getMessage());
                        $this->shortresponse(500, $e->getMessage());
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

    /**
     * @throws RuntimeException when saving page fails
     *
     * @todo This should not throw RuntimeException but manage the exception itself and log friendly error
     */
    public function add(string $page): void
    {
        if (!Model::idcheck($page)) {
            $this->shortresponse(406, 'ID is not valid');
        }
        if (!$this->user->iseditor()) {
            $this->shortresponse(401, 'User cannot create pages');
        }
        if ($this->pagemanager->exist($page)) {
            $this->shortresponse(405, 'ID is already used');
        }

        $this->page = $this->pagemanager->newpage(array_merge($this->recievejson(), ['id' => $page]));
        $this->page->addauthor($this->user->id());
        $this->pagemanager->add($this->page);
        $user = $this->user->id();
        Logger::info("User '$user' successfully added Page '$page'");
    }

    /**
     * @throws RuntimeException when saving page fails
     *
     * @todo This should not throw RuntimeException but manage the exception itself and log friendly error
     */
    public function put(string $page): void
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
        $this->page = $this->pagemanager->newpage(array_merge($this->recievejson(), ['id' => $page]));
        if (!$exist) { // If it's a page creation, add the user as an author
            $this->page->addauthor($this->user->id());
        }
        $this->pagemanager->add($this->page);
        http_response_code($exist ? 200 : 201);
    }

    public function delete(string $page): void
    {
        if ($this->importpage($page)) {
            if ($this->user->issupereditor() || $this->page->authors() === [$this->user->id()]) {
                try {
                    $this->pagemanager->delete($this->page);
                    $user = $this->user->id();
                    Logger::info("User '$user' uccessfully deleted Page '$page'");
                    $this->shortresponse(200);
                } catch (Filesystemexception $e) {
                    Logger::warning("Error while deleting Page '$page'" . $e->getMessage());
                    $this->shortresponse(200, $e->getMessage());
                } catch (Databaseexception $e) {
                    Logger::error("Could not delete Page $page: " . $e->getMessage());
                    $this->shortresponse(500, $e->getMessage());
                }
            } else {
                http_response_code(401);
            }
        }
    }

    public function list(): void
    {
        if (!$this->user->iseditor()) {
            http_response_code(401);
        }
        http_response_code(200);
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($this->pagemanager->list());
    }

    public function query(): void
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
