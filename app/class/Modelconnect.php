<?php

namespace Wcms;

use Ahc\Jwt\JWT;
use Ahc\Jwt\JWTException;
use DateTime;
use donatj\UserAgent\UserAgentParser;
use LogicException;
use RuntimeException;
use Wcms\Exception\Database\Notfoundexception;
use Wcms\Exception\Databaseexception;

class Modelconnect extends Model
{
    /**
     * Check presence of a Bearer Auth JWT
     *
     * @throws RuntimeException if bearer auth did'nt worked
     */
    public function bearerauth(Modeluser $usermanager): User
    {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            throw new RuntimeException('missing Authorization HTTP header');
        }
        $header = $headers['Authorization'];
        $words = explode(' ', $header, 2);
        if ($words[0] !== 'Bearer' || !isset($words[1])) {
            throw new RuntimeException('malformed Bearer Authorization header');
        }
        $jwt = $words[1];
        try {
            $data = $this->readjwt($jwt);
            $userid = $data['userid'];
            $user = $usermanager->get($userid);
            if ($user->checksession($data['wsession'])) {
                return $user;
            } else {
                throw new RuntimeException('wrong credentials');
            }
        } catch (Databaseexception $e) {
            throw new RuntimeException('could not get user in database: ' . $e->getMessage());
        } catch (RuntimeException $e) {
            throw new RuntimeException('malformed JWT: ' . $e->getMessage());
        }
    }

    /**
     * @param string $userid
     * @param string $wsession
     * @param int $conservation
     * @throws RuntimeException if secret key is not set or cant send cookie
     */
    public function createauthcookie(string $userid, string $wsession, int $conservation): void
    {
        $jwt = $this->createjwt($userid, $wsession);
        $options = [
            'expires' => time() + $conservation * 24 * 3600,
            'path' => '/' . Config::basepath(),
            'domain' => '',
            'secure' => Config::issecure(),
            'httponly' => true,
            'samesite' => 'Strict'
        ];
        $success = setcookie('rememberme', $jwt, $options);
        if (!$success) {
            throw new RuntimeException("Remember me cookie cannot be created");
        }
    }

    /**
     * @throws RuntimeException if secret key is not set
     */
    public function createjwt(string $userid, string $wsession): string
    {
        $datas = [
            "userid" => $userid,
            "wsession" => $wsession
        ];
        if (empty(Config::secretkey())) {
            throw new RuntimeException("Secret Key not set");
        }
        $jwt = new JWT(Config::secretkey(), 'HS256', 3600 * 24 * 365); // expire in one year
        return $jwt->encode($datas);
    }

    /**
     * Get decoded cookie using JWT
     *
     * @return array{'userid': string, 'wsession': string}
     *
     * @throws RuntimeException             If JWT token decode failed or auth cookie is unset
     */
    public function checkcookie(): array
    {
        if (empty($_COOKIE['rememberme'])) {
            throw new RuntimeException('Auth cookie is unset');
        }
        return $this->readjwt($_COOKIE['rememberme']);
    }

    /**
     * @return array{'userid': string, 'wsession': string}
     *
     * @throws RuntimeException If JWT token decode failed or is expired
     */
    public function readjwt(string $token): array
    {
        try {
            $jwt = new JWT(Config::secretkey());
            return $jwt->decode($token);
        } catch (JWTException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * Delete authentication cookie
     */
    public function deleteauthcookie(): void
    {
        $_COOKIE['rememberme'] = [];
    }

    /**
     * Try to login an user against local password or LDAP
     * If LDAP config is valid and user not in database, try to create a new user if auth succeeded
     *
     * @throws RuntimeException if login failed or database connections error occured
     */
    public function login(string $username, string $password): User
    {
        $usermanager = new Modeluser();

        try {
            $user = $usermanager->get($username);
        } catch (Notfoundexception $e) {
            if (!Config::isldap()) {
                throw new RuntimeException('get: ' . $e->getMessage());
            }

            // LDAP config is defined: try to create a new user
            try {
                $user = $this->newldapuser($username, $password);
                $usermanager->add($user);
                Logger::info("new LDAP user '%s' created", $user->id());
                return $user;
            } catch (RuntimeException $e) {
                throw new RuntimeException('create LDAP user: ' . $e->getMessage());
            }
        } catch (Databaseexception $e) {
            throw new RuntimeException('get: ' . $e->getMessage());
        }

        // check if account is expired
        if (
            $user->expiredate() !== false &&
            $user->expiredate() < new DateTime() &&
            $user->level() < 10
        ) {
            throw new RuntimeException('account expired');
        }

        // check password
        if ($user->isldap()) {
            if (!Config::isldap()) {
                throw new RuntimeException('user require LDAP credentials: missing or invalid LDAP config');
            }
            try {
                $success = $this->authldap($user->id(), $password);
            } catch (RuntimeException $e) {
                throw new RuntimeException('LDAP auth: ' . $e->getMessage());
            }
        } else {
            $success = $usermanager->passwordcheck($user, $password);
        }

        // wrong password
        if (!$success) {
            throw new RuntimeException('wrong password');
        }

        $user->connectcounter();
        $usermanager->update($user);

        return $user;
    }

    /**
     * Try to authenticate an user against LDAP connection
     * Config LDAP should be checked before
     *
     * @throws RuntimeException if LDAP connection failed
     */
    protected function authldap(string $username, string $password): bool
    {
        if (!Config::isldap()) {
            throw new LogicException('LDAP config not complete');
        }
        $ldap = new Modelldap(Config::ldapserver(), Config::ldaptree(), Config::ldapu());
        $success = $ldap->auth($username, $password);
        $ldap->disconnect();
        return $success;
    }

    /**
     * Try to create a new user if LDAP auth succeeded
     * Create the default personnal bookmark
     *
     * @throws RuntimeException
     */
    protected function newldapuser(string $username, string $password): User
    {
        // authenticate over LDAP
        if (!$this->authldap($username, $password)) {
            throw new RuntimeException('LDAP auth failed');
        }

        // create a new User
        $user = new User(['password' => null, 'level' => Config::ldapuserlevel(), 'id' => $username]);
        $user->connectcounter();

        // create personnal bookmark
        try {
            $bookmarkmanager = new Modelbookmark();
            $bookmarkmanager->addauthorbookmark($user);
        } catch (RuntimeException $e) {
            // bookmark creation failure should not stop the process
            Logger::error("personnal bookmark creation for user '%s': %s", $user->id(), $e);
        }

        return $user;
    }

    /**
     * Try to remember user
     * add new session to user sessions and update User to database
     *
     * @return string W session ID
     *
     * @throws RuntimeException If cookie conservation time of User is not positive or other error
     */
    public function remember(User $user): string
    {
        if ($user->cookie() <= 0) {
            throw new RuntimeException('Can\'t remember you: cookie conservation time is set to 0 days');
        }

        $uaparser = new UserAgentParser();
        $ua = $uaparser->parse();
        $browser = $ua->browser() ?? 'unknwown-browser';
        $platform = $ua->platform() ?? 'unknwown-platform';
        $msg = date('Y-m-d') . "/$platform/$browser/" . $user->cookie() . "d";
        $wsessionid = $user->newsession($msg);
        $this->createauthcookie(
            $user->id(),
            $wsessionid,
            $user->cookie()
        );
        $usermanager = new Modeluser();
        $usermanager->add($user);
        return $wsessionid;
    }
}
