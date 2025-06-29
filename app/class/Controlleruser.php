<?php

namespace Wcms;

use AltoRouter;
use RuntimeException;
use Wcms\Exception\Databaseexception;
use Wcms\Exception\Database\Notfoundexception;

class Controlleruser extends Controller
{
    public function __construct(AltoRouter $router)
    {
        parent::__construct($router);

        if ($this->user->isvisitor()) {
            http_response_code(401);
            $this->showtemplate('connect', ['route' => 'user']);
            exit;
        }
    }

    public function desktop(): void
    {
        if ($this->user->isadmin()) {
            $datas['userlist'] = $this->usermanager->getlister();
            $this->showtemplate('user', $datas);
        } else {
            http_response_code(403);
            $this->showtemplate('forbidden');
        }
    }

    public function add(): void
    {
        if ($this->user->isadmin() && isset($_POST['id'])) {
            $user = new User($_POST);
            if (empty($user->id()) || $this->usermanager->exist($user)) {
                $this->sendflashmessage('Error: problem with ID', self::FLASH_ERROR);
                $this->routedirect('user');
            }
            if ($user->password() === null) {
                $this->sendflashmessage('Error: invalid password', self::FLASH_ERROR);
                $this->routedirect('user');
            }
            if ($user->passwordhashed()) {
                $user->hashpassword();
            }
            try {
                $this->usermanager->add($user);
                $this->sendflashmessage('User successfully added', self::FLASH_SUCCESS);
            } catch (Databaseexception $e) {
                $this->sendflashmessage($e->getMessage(), self::FLASH_ERROR);
                Logger::errorex($e);
            }
            try {
                $bookmarkmanager = new Modelbookmark();
                $bookmarkmanager->addauthorbookmark($user);
            } catch (RuntimeException $e) {
                $this->sendflashmessage(
                    'error while creating user\'s personnal author bookmark',
                    self::FLASH_WARNING
                );
                Logger::errorex($e);
            }
            $this->routedirect('user');
        }
    }

    public function edit(): void
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
                        $this->sendflashmessage('User successfully deleted', self::FLASH_SUCCESS);
                        $this->routedirect('user');

                    case 'update':
                        $this->update($_POST);
                        $this->sendflashmessage('User successfully updated', self::FLASH_SUCCESS);
                        $this->routedirect('user');
                }
            } catch (RuntimeException $e) {
                $this->sendflashmessage('Error : ' . $e->getMessage(), self::FLASH_ERROR);
                $this->routedirect('user');
            }
        } else {
            http_response_code(403);
            $this->showtemplate('forbidden');
            exit;
        }
    }

    // ________________________________ F U N _________________________________________

    /**
     * @param array<string, mixed> $datas
     *
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
     * @param array<string, mixed> $datas
     *
     * @throws Notfoundexception            If User is not found in the database
     * @throws Databaseexception            If an error occured with database
     * @throws Runtimeexception             In case of other various problems
     *
     * @todo move this to Modeluser
     */
    protected function update(array $datas): void
    {
        $user = $this->usermanager->get($datas['id']);
        $userupdate = clone $user;
        $userupdate->hydrate($datas);
        if (
            !empty($datas['password'])
            && !$userupdate->setpassword($datas['password'])
        ) {
            throw new RuntimeException('Unvalid password');
        }

        if (
            $user->level() === 10
            && $userupdate->level() !== 10
            && $this->user->id() === $user->id()
        ) {
            throw new RuntimeException('You cannot quit administration job');
        }

        if ($userupdate->password() !== $user->password() && $user->passwordhashed()) {
            $userupdate->setpasswordhashed(false);
        }
        if ($userupdate->passwordhashed() && !$user->passwordhashed()) {
            $userupdate->hashpassword();
        }
        if ($user->isldap()) {
            $userupdate->setpassword(null);
            $userupdate->setpasswordhashed(false);
        }
        $this->usermanager->update($userupdate);
    }
}
