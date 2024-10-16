<?php

namespace Wcms;

use RuntimeException;
use Wcms\Exception\Database\Notfoundexception;

class Controllerconnect extends Controller
{
    /** @var Modelconnect */
    protected $modelconnect;

    public function log(): void
    {
        if (isset($_POST['log'])) {
            $id = $_POST['id'] ?? null;
            $route = $_POST['route'] ?? 'home';
            if ($_POST['log'] === 'login') {
                $this->login($route, $id);
            } elseif ($_POST['log'] === 'logout') {
                $this->logout($route, $id);
            }
        }
    }


    public function connect(): void
    {
        if (isset($_SESSION['pageupdate'])) {
            $pageupdate['route'] = 'pageedit';
            $pageupdate['id'] = $_SESSION['pageupdate']['id'];
        } else {
            $pageupdate = ['route' => 'home'];
        }
        $this->showtemplate('connect', $pageupdate);
    }




    /**
     * Will login an user using POST datas and redirect
     *
     * @param string $route     For redirection
     * @param ?string $paramid  For redirection (optionnal, can be used for pages redirection)
     */
    protected function login(string $route, ?string $paramid = null): void
    {
        if (!empty($_POST['pass']) && !empty($_POST['user'])) {
            $userid = $_POST['user'];
            try {
                $this->user = $this->usermanager->get($userid); // May throw DatabaseException
                if (!$this->usermanager->passwordcheck($this->user, $_POST['pass'])) {
                    $userid = $this->user->id();
                    $this->sendflashmessage("Wrong credentials", self::FLASH_ERROR);
                    Logger::error("wrong credential for user : '$userid' when attempting to loggin");
                } elseif (
                    $this->user->expiredate() !== false &&
                    $this->user->expiredate('date') < $this->now &&
                    $this->user->level() < 10
                ) {
                    $this->sendflashmessage("Account expired", self::FLASH_ERROR);
                } else {
                    $this->user->connectcounter();
                    $this->usermanager->add($this->user);
                    $this->servicesession->setuser($this->user->id());
                    $this->sendflashmessage("Successfully logged in as " . $this->user->id(), self::FLASH_SUCCESS);

                    if (!empty($_POST['rememberme'])) {
                        if ($this->user->cookie() > 0) {
                            $this->modelconnect = new Modelconnect();
                            $wsessionid = $this->user->newsession();
                            $this->modelconnect->createauthcookie(
                                $this->user->id(),
                                $wsessionid,
                                $this->user->cookie()
                            );
                            $this->usermanager->add($this->user);
                            $this->servicesession->setwsessionid($wsessionid);
                        } else {
                            $message = "Can't remember you beccause user cookie conservation time is set to 0 days";
                            $this->sendflashmessage($message, self::FLASH_WARNING);
                        }
                    }
                }
            } catch (Notfoundexception $e) {
                $this->sendflashmessage("Wrong credentials", self::FLASH_ERROR);
                Logger::errorex($e);
            } catch (RuntimeException $e) {
                $message = "Can't create authentification cookie : $e";
                $this->sendflashmessage($message, self::FLASH_WARNING);
                Logger::error($message);
            }
        }
        if (is_string($paramid)) {
            $this->routedirect($route, ['page' => $paramid]);
        } else {
            $this->routedirect($route);
        }
    }

    public function logout($route, $id = null): void
    {
        $this->disconnect();

        if ($id !== null && $route !== 'home') {
            $this->routedirect($route, ['page' => $id]);
        } else {
            $this->routedirect($route);
        }
    }
}
