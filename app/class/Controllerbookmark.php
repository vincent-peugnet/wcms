<?php

namespace Wcms;

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
        if ($this->user->isadmin() && isset($_POST['id'])) {
            try {
                $bookmark = new Bookmark($_POST);
                $this->bookmarkmanager->add($bookmark);
                Model::sendflashmessage(
                    "Bookmark \"" . $bookmark->id() . "\" succesfully created",
                    Model::FLASH_SUCCESS
                );
            } catch (RuntimeException $e) {
                Model::sendflashmessage("Error while creating bookmark : " . $e->getMessage(), Model::FLASH_ERROR);
            }
            $this->routedirect($_POST['route'] ?? 'home');
        }
    }

    public function delete()
    {
        if ($this->user->isadmin() && isset($_POST['id'])) {
            if (!$_POST['confirmdelete']) {
                Model::sendflashmessage("Confirm delete has not been checked", Model::FLASH_WARNING);
            } else {
                try {
                    $bookmark = $this->bookmarkmanager->get($_POST['id']);
                    $this->bookmarkmanager->delete($bookmark);
                } catch (RuntimeException $e) {
                    Model::sendflashmessage(
                        "Error while trying to delete bookmark: " . $e->getMessage(),
                        Model::FLASH_ERROR
                    );
                }
            }
        }
        $this->routedirect($_POST['route']);
    }
}
