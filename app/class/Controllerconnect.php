<?php

namespace Wcms;

use RuntimeException;

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
                $this->login();
            } elseif ($_POST['log'] === 'logout') {
                $this->logout();
            }
            if (is_string($id)) {
                $this->routedirect($route, ['page' => $id]);
            } else {
                $this->routedirect($route);
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
     * Will try to login an user using POST datas
     */
    protected function login(): void
    {
        if (!empty($_POST['pass']) && !empty($_POST['user'])) {
            $this->modelconnect = new Modelconnect();
            $userid = $_POST['user'];
            $pass = false;

            try {
                $this->user = $this->usermanager->get($userid); // May throw DatabaseException
            } catch (RuntimeException $e) {
                if (Config::ldapuserlevel() > 0) {
                    $this->user = new User(['password' => null, 'level' => Config::ldapuserlevel(), 'id' => $userid]);
                } else {
                    $this->sendflashmessage('Wrong credentials', self::FLASH_ERROR);
                    Logger::errorex($e);
                    return;
                }
            }

            if ($this->user->isldap()) {
                try {
                    $ldap = new Modelldap(Config::ldapserver(), Config::ldaptree(), Config::ldapu());
                    $pass = $ldap->auth($userid, $_POST['pass']);
                    $ldap->disconnect();
                } catch (RuntimeException $e) {
                    $this->sendflashmessage('Error with LDAP connection', self::FLASH_ERROR);
                    Logger::errorex($e);
                    return;
                }
            } else {
                $pass = $this->usermanager->passwordcheck($this->user, $_POST['pass']);
            }

            if (!$pass) {
                $this->sendflashmessage("Wrong credentials", self::FLASH_ERROR);
                return;
            }

            if (
                $this->user->expiredate() !== false &&
                $this->user->expiredate('date') < $this->now &&
                $this->user->level() < 10
            ) {
                $this->sendflashmessage("Account expired", self::FLASH_ERROR);
                return;
            }

            try {
                $this->user->connectcounter();
                $this->usermanager->add($this->user);
                $this->servicesession->setuser($this->user->id());
                $this->sendflashmessage("Successfully logged in as " . $this->user->id(), self::FLASH_SUCCESS);

                if (!empty($_POST['rememberme'])) {
                    if ($this->user->cookie() > 0) {
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
            } catch (RuntimeException $e) {
                $message = "Can't create authentification cookie : $e";
                $this->sendflashmessage($message, self::FLASH_WARNING);
                Logger::error($message);
            }
        }
    }

    protected function logout(): void
    {
        $this->disconnect();
    }
}
