<?php

namespace Wcms;

use RuntimeException;
use Wcms\Exception\Missingextensionexception;

class Modelldap extends Model
{
    protected string $ldapserver;
    protected string $tree;
    protected string $u;


    /** @var mixed $connection resource (PHP 7) or LDAPConnection (PHP 8)*/
    protected $connection;

    protected const LDAP_INVALID_CREDENTIALS = 0x31;

    /**
     * @param string $ldapserver            LDAP server, like `ldap://server.tld:port` or just `ldap://localhost`
     * @param string $tree                  LDAP structure tree without the username part.
     *                                      Like `ou=people,dc=server,dc=tld`
     * @param string $u                     Username storing name, something like `uid`.
     *
     * @throws RuntimeException if LDAP server syntax did pass the sanity test
     * @throws Missingextensionexception if LDAP extension is not installed
     */
    public function __construct(string $ldapserver, string $tree, string $u)
    {
        if (!extension_loaded('ldap')) {
            throw new Missingextensionexception('PHP LDAP extension is not installed');
        }
        $this->ldapserver = $ldapserver;
        $this->connection = @ldap_connect($this->ldapserver);
        if ($this->connection === false) {
            throw new RuntimeException('bad LDAP server syntax');
        }
        $this->tree = $tree;
        $this->u = $u;
        ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
    }

    /**
     * Try to authenticate user against CLUB1 local LDAP server
     *
     * @param string $username
     * @param string $password
     *
     * @return bool                         indicating if auth is a success
     *
     * @throws RuntimeException             If LDAP connection failed
     */
    public function auth(string $username, string $password): bool
    {
        $binddn = "$this->u=$username,$this->tree";

        $ldapbind = @ldap_bind($this->connection, $binddn, $password);
        if ($ldapbind === false) {
            $errno = ldap_errno($this->connection);
            switch ($errno) {
                case self::LDAP_INVALID_CREDENTIALS:
                    return false;
            }
            throw new RuntimeException(ldap_err2str($errno));
        }
        return true;
    }

    public function disconnect(): void
    {
        ldap_close($this->connection);
    }
}
