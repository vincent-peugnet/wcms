<?php

namespace Wcms;

use RuntimeException;
use Wcms\Exception\Databaseexception;
use Wcms\Exception\Database\Notfoundexception;

class Controlleruser extends Controller
{
    public function __construct($router)
    {
        parent::__construct($router);

        if ($this->user->isvisitor()) {
            http_response_code(401);
            $this->showtemplate('connect', ['route' => 'user']);
            exit;
        }
    }

    public function desktop()
    {
        if ($this->user->isadmin()) {
            $datas['userlist'] = $this->usermanager->getlister();
            $this->showtemplate('user', $datas);
        } else {
            http_response_code(403);
            $this->showtemplate('forbidden');
        }
    }

    public function add()
    {
        if ($this->user->isadmin() && isset($_POST['id'])) {
            $user = new User($_POST);
            if (empty($user->id()) || $this->usermanager->exist($user)) {
                Model::sendflashmessage('Error: problem with ID', Model::FLASH_ERROR);
                $this->routedirect('user');
            }
            if (!$user->validpassword()) {
                Model::sendflashmessage('Error: invalid password', Model::FLASH_ERROR);
                $this->routedirect('user');
            }
            if ($user->passwordhashed()) {
                $user->hashpassword();
            }
            try {
                $this->usermanager->add($user);
                Model::sendflashmessage('User successfully added', Model::FLASH_SUCCESS);
            } catch (Databaseexception $e) {
                Model::sendflashmessage($e->getMessage(), Model::FLASH_ERROR);
            }
            $this->addauthorbookmark($user);
            $this->routedirect('user');
        }
    }

    public function edit()
    {
        if ($this->user->isadmin() && isset($_POST['action'])) {
            try {
                switch ($_POST['action']) {
                    case 'delete':
                        $this->delete($_POST);
                        break;

                    case 'confirmdelete':
                        $user = new User($_POST);
                        $this->usermanager->delete($user);
                        $this->routedirect('user');
                        break;

                    case 'update':
                        $this->update($_POST);
                        Model::sendflashmessage('User successfully updated', Model::FLASH_SUCCESS);
                        $this->routedirect('user');
                        break;
                }
            } catch (RuntimeException $e) {
                Model::sendflashmessage('Error : ' . $e->getMessage(), Model::FLASH_ERROR);
            }
        } else {
            http_response_code(403);
            $this->showtemplate('forbidden');
            exit;
        }
    }

    // ________________________________ F U N _________________________________________

    /**
     * @throws Notfoundexception            If user is not found in the database
     */
    protected function delete(array $datas): void
    {
        $user = new User($datas);
        $user = $this->usermanager->get($user);
        if ($user->id() === $this->user->id()) {
            $this->showtemplate('userconfirmdelete', ['userdelete' => $user, 'candelete' => false]);
        } else {
            $this->showtemplate('userconfirmdelete', ['userdelete' => $user, 'candelete' => true]);
        }
    }

    /**
     * @throws Notfoundexception            If User is not found in the database
     * @throws Databaseexception            If an error occured with database
     * @throws Runtimeexception             In case of other various problems
     */
    protected function update(array $datas): void
    {
        $user = $this->usermanager->get($datas['id']);
        $userupdate = clone $user;
        $userupdate->hydrate($datas);
        if (
            !empty($datas['password'])
            && (empty($userupdate->password())
            || !$userupdate->validpassword())
        ) {
            throw new RuntimeException('Unvalid password');
        }

        if (
            $user->level() === 10
            && $userupdate->level() !== 10
            && $this->user->id() === $user->id()
        ) {
            throw new RuntimeException('You cannot quit administration job');
        } else {
            if ($userupdate->password() !== $user->password() && $user->passwordhashed()) {
                $userupdate->setpasswordhashed(false);
            }
            if ($userupdate->passwordhashed() && !$user->passwordhashed()) {
                $userupdate->hashpassword();
            }
            $this->usermanager->update($userupdate);
        }
    }

    /**
     * Create a bookmark that filter pages where the user is an author.
     * Send a flash message in case of error.
     *
     * @param User $user                    The concerned user (need to be already added in database)
     */
    protected function addauthorbookmark(User $user): void
    {
        try {
            $bookmarkmanager = new Modelbookmark();
            $userbookmark = new Bookmark();
            $uid = $user->id();
            $userbookmark->init(
                "$uid-is-author",
                "?authorfilter[0]=$uid&submit=filter",
                '👤',
                "$uid's pages",
                "Pages where $uid is listed as an author",
            );
            $userbookmark->setuser($user->id());
            $bookmarkmanager->add($userbookmark);
        } catch (RuntimeException $e) {
            Model::sendflashmessage(
                'Could not create personnal user author bookmark: ' . $e->getMessage(),
                Model::FLASH_ERROR
            );
        }
    }
}
