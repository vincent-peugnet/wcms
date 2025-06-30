<?php

namespace Wcms;

use AltoRouter;
use DOMException;
use RuntimeException;

class Controllerbookmark extends Controller
{
    protected ?Modelbookmark $bookmarkmanager = null;

    public function __construct(AltoRouter $router)
    {
        parent::__construct($router);
        $this->bookmarkmanager = new Modelbookmark();
    }

    public function add(): never
    {
        if ($this->user->iseditor() && isset($_POST['id'])) {
            try {
                $bookmark = new Bookmark($_POST);
                if ($this->bookmarkmanager->exist($bookmark)) {
                }
                if (!$bookmark->ispublic() && $this->user->id() === $bookmark->user() || $this->user->isadmin()) {
                    $bookmark->setname($_POST['id']);
                    $this->bookmarkmanager->add($bookmark);
                    $this->sendflashmessage(
                        "Bookmark \"" . $bookmark->id() . "\" succesfully created",
                        self::FLASH_SUCCESS
                    );
                }
            } catch (RuntimeException $e) {
                $this->sendflashmessage("Error while creating bookmark : " . $e->getMessage(), self::FLASH_ERROR);
            }
        }
        $this->routedirect($_POST['route'] ?? 'home');
    }

    public function delete(): never
    {
        if ($this->user->iseditor() && isset($_POST['id'])) {
            if (!$_POST['confirmdelete']) {
                $this->sendflashmessage("Confirm delete has not been checked", self::FLASH_WARNING);
            } else {
                try {
                    $bookmark = $this->bookmarkmanager->get($_POST['id']);
                    if (!$bookmark->ispublic() && $this->user->id() === $bookmark->user() || $this->user->isadmin()) {
                        $this->bookmarkmanager->delete($bookmark);
                        $this->sendflashmessage("Bookmark successfully deleted", self::FLASH_SUCCESS);
                    }
                } catch (RuntimeException $e) {
                    $this->sendflashmessage(
                        "Error while trying to delete bookmark: " . $e->getMessage(),
                        self::FLASH_ERROR
                    );
                }
            }
        }
        $this->routedirect($_POST['route'] ?? 'home');
    }

    public function update(): never
    {
        if ($this->user->iseditor() && isset($_POST['id'])) {
            try {
                $bookmark = $this->bookmarkmanager->get($_POST['id']);
                if (!$bookmark->ispublic() && $this->user->id() === $bookmark->user() || $this->user->isadmin()) {
                    $bookmark->hydrateexception($_POST);
                    $this->bookmarkmanager->update($bookmark);
                    $this->sendflashmessage("Bookmark successfully updated", self::FLASH_SUCCESS);
                }
            } catch (RuntimeException $e) {
                $this->sendflashmessage("Impossible to update bookmark: " . $e->getMessage());
            }
        }
        $this->routedirect($_POST['route'] ?? 'home');
    }

    /**
     * Publish RSS atom file associated to the bookmark
     *
     * @param string $bookmark              Id of the bookmark
     */
    public function publish(string $bookmark): never
    {
        if ($this->user->issupereditor()) {
            try {
                $bookmark = $this->bookmarkmanager->get($bookmark);
                if ($bookmark->ispublic()) {
                    $rss = new Servicerss($this->router);
                    $rss->publishbookmark($bookmark);

                    $bookmark->setpublished(true);
                    $this->bookmarkmanager->update($bookmark);

                    $this->sendflashmessage('RSS feed successfully published', self::FLASH_SUCCESS);
                }
            } catch (RuntimeException | DOMException $e) {
                $this->sendflashmessage($e->getMessage(), self::FLASH_ERROR);
            }
        }
        $this->routedirect('home');
    }

    public function unpublish(string $bookmark): never
    {
        if ($this->user->issupereditor()) {
            try {
                $bookmark = $this->bookmarkmanager->get($bookmark);
                if ($bookmark->ispublished()) {
                    Servicerss::removeatom($bookmark->id());
                    $bookmark->setpublished(false);
                    $this->sendflashmessage("Bookmark is not published anymore", self::FLASH_SUCCESS);
                }
                $this->bookmarkmanager->update($bookmark);
            } catch (RuntimeException $e) {
                $this->sendflashmessage($e->getMessage(), self::FLASH_ERROR);
            }
        }
        $this->routedirect('home');
    }
}
