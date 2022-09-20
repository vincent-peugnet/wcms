<?php

namespace Wcms;

use DOMException;
use Exception;
use RuntimeException;

class Controllerbookmark extends Controller
{
    protected ?Modelbookmark $bookmarkmanager = null;

    public function __construct($router)
    {
        parent::__construct($router);
        $this->bookmarkmanager = new Modelbookmark();
    }

    public function add()
    {
        if ($this->user->iseditor() && isset($_POST['id'])) {
            try {
                $bookmark = new Bookmark($_POST);
                if ($this->bookmarkmanager->exist($bookmark)) {
                }
                if (!$bookmark->ispublic() && $this->user->id() === $bookmark->user() || $this->user->isadmin()) {
                    $bookmark->setname($_POST['id']);
                    $this->bookmarkmanager->add($bookmark);
                    Model::sendflashmessage(
                        "Bookmark \"" . $bookmark->id() . "\" succesfully created",
                        Model::FLASH_SUCCESS
                    );
                }
            } catch (RuntimeException $e) {
                Model::sendflashmessage("Error while creating bookmark : " . $e->getMessage(), Model::FLASH_ERROR);
            }
            $this->routedirect($_POST['route'] ?? 'home');
        }
    }

    public function delete()
    {
        if ($this->user->iseditor() && isset($_POST['id'])) {
            if (!$_POST['confirmdelete']) {
                Model::sendflashmessage("Confirm delete has not been checked", Model::FLASH_WARNING);
            } else {
                try {
                    $bookmark = $this->bookmarkmanager->get($_POST['id']);
                    if (!$bookmark->ispublic() && $this->user->id() === $bookmark->user() || $this->user->isadmin()) {
                        $this->bookmarkmanager->delete($bookmark);
                        Model::sendflashmessage("Bookmark successfully deleted", Model::FLASH_SUCCESS);
                    }
                } catch (RuntimeException $e) {
                    Model::sendflashmessage(
                        "Error while trying to delete bookmark: " . $e->getMessage(),
                        Model::FLASH_ERROR
                    );
                }
            }
        }
        $this->routedirect($_POST['route'] ?? 'home');
    }

    public function update()
    {
        if ($this->user->iseditor() && isset($_POST['id'])) {
            try {
                $bookmark = $this->bookmarkmanager->get($_POST['id']);
                if (!$bookmark->ispublic() && $this->user->id() === $bookmark->user() || $this->user->isadmin()) {
                    $bookmark->hydrateexception($_POST);
                    $this->bookmarkmanager->update($bookmark);
                    Model::sendflashmessage("Bookmark successfully updated", Model::FLASH_SUCCESS);
                }
            } catch (RuntimeException $e) {
                Model::sendflashmessage("Impossible to update bookmark: " . $e->getMessage());
            }
        }
        $this->routedirect($_POST['route'] ?? 'home');
    }

    /**
     * Publish RSS atom file associated to the bookmark
     *
     * @param string $bookmark              Id of the bookmark
     */
    public function publish(string $bookmark)
    {
        if ($this->user->issupereditor()) {
            try {
                $bookmark = $this->bookmarkmanager->get($bookmark);
            } catch (RuntimeException $e) {
                Model::sendflashmessage($e, Model::FLASH_ERROR);
                $this->routedirect('home');
            }
            if ($bookmark->ispublic()) {
                $rss = new Optrss();
                $rss->parsehydrate($bookmark->query());

                $pagelist = $this->pagemanager->pagelist();
                $pagetable = $this->pagemanager->pagetable($pagelist, $rss, '', []);

                $render = new Modelrender($this->router);

                try {
                    $xml = $rss->render($pagetable, $bookmark, $render);
                    Model::writefile(Model::ASSETS_ATOM_DIR . $bookmark->id() . '.xml', $xml);
                } catch (DOMException $e) {
                    Model::sendflashmessage(
                        'Error while creating RSS XML file: ' . $e->getMessage(),
                        Model::FLASH_ERROR
                    );
                }
            }
        } else {
            // throw a 403 forbiden
        }
        $this->routedirect('home');
    }
}
