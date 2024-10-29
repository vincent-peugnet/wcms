<?php

namespace Wcms;

use RuntimeException;

class Modelclub1ldap extends Model
{
    protected string $ldapserver = 'ldap://localhost:389';

    protected string $d = 'ou=People,dc=club1,dc=fr';
    protected string $u = 'uid';


    /** @var mixed $connection resource (PHP 7) or LDAPConnection (PHP 8)*/
    protected $connection;

    private const LDAP_INVALID_CREDENTIALS = 0x31;

    /**
     * @throws RuntimeException
     */
    public function __construct()
    {
        $this->connection = @ldap_connect($this->ldapserver);
        if ($this->connection === false) {
            throw new RuntimeException('bad LDAP server syntax');
        }
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
        $binddn = "$this->u=$username,$this->d";

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
