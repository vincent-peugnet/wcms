<?php

namespace Wcms;

use DateTime;
use donatj\UserAgent\UserAgentParser;
use Firebase\JWT\JWT;
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
        return JWT::encode($datas, Config::secretkey());
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
     * @throws RuntimeException If JWT token decode failed
     */
    public function readjwt(string $jwt): array
    {
        $obj = JWT::decode($jwt, Config::secretkey(), ['HS256']);
        return get_object_vars($obj);
    }

    /**
     * Delete authentication cookie
     */
    public function deleteauthcookie(): void
    {
        $_COOKIE['rememberme'] = [];
    }

    /**
     * Will try to login an user against local password or LDAP
     *
     * @throws RuntimeException if login failed or database connections occured
     */
    public function login(string $username, string $password): User
    {
        $usermanager = new Modeluser();

        try {
            $user = $usermanager->get($username);

            if (
                $user->expiredate() !== false &&
                $user->expiredate() < new DateTime() &&
                $user->level() < 10
            ) {
                throw new RuntimeException('Account expired');
            }

            if ($user->isldap()) {
                $success = $this->authldap($username, $password);
            } else {
                $success = $usermanager->passwordcheck($user, $password);
            }

            if (!$success) {
                throw new RuntimeException('Wrong credentials');
            }
        } catch (Notfoundexception $e) {
            if (Config::isldap() && $this->authldap($username, $password)) {
                // create a new User and add a personnal bookmark
                $user = new User(['password' => null, 'level' => Config::ldapuserlevel(), 'id' => $username]);
                $bookmarkmanager = new Modelbookmark();
                $bookmarkmanager->addauthorbookmark($user);
            } else {
                throw new RuntimeException('Wrong credentials');
            }
        }

        $user->connectcounter();
        $usermanager->add($user);

        return $user;
    }

    /**
     * @throws RuntimeException if LDAP is not activated in Config or LDAP is not functionnal
     */
    protected function authldap(string $username, string $password): bool
    {
        if (!Config::isldap()) {
            throw new RuntimeException('Error with LDAP connection');
        }
        $ldap = new Modelldap(Config::ldapserver(), Config::ldaptree(), Config::ldapu());
        $success = $ldap->auth($username, $password);
        $ldap->disconnect();
        return $success;
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
