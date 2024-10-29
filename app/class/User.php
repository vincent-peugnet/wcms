<?php

namespace Wcms;

use DateTimeImmutable;
use DateTimeZone;
use RuntimeException;

class User extends Item
{
    protected string $id = '';
    protected int $level = 0;
    protected string $signature = '';
    protected ?string $password = null;
    protected bool $passwordhashed = false;

    /** @var string $name Displayed name */
    protected string $name = '';

    /** @var string $url Account associated URL */
    protected string $url = '';

    /** @var int $cookie Conservation time */
    protected int $cookie = 30;

    /** @var string[] $columns List of displayed columns */
    protected array $columns = ['title', 'datemodif', 'datecreation', 'secure', 'visitcount'];

    /** @var int $connectcount Connections counter */
    protected int $connectcount = 0;

    /** @var DateTimeImmutable|false $expiredate */
    protected $expiredate = false;

    /** @var string[] sessions */
    protected array $sessions = [];

    public const HOME_COLUMNS = [
        'favicon',
        'download',
        'tag',
        'title',
        'description',
        'linkto',
        'geolocalisation',
        'datemodif',
        'datecreation',
        'date',
        'secure',
        'authors',
        'visitcount',
        'editcount',
        'displaycount',
        'version',
    ];


    public function __construct($datas = [])
    {
        $this->hydrate($datas);
    }

    /**
     * Indicate if User is authenticated using LDAP.
     * It is if password is set to null.
     */
    public function isldap(): bool
    {
        return (is_null($this->password));
    }

    // _________________________ G E T _______________________

    public function id()
    {
        return $this->id;
    }

    public function level()
    {
        return $this->level;
    }

    public function password()
    {
        return $this->password;
    }

    public function signature()
    {
        return $this->signature;
    }

    public function passwordhashed()
    {
        return $this->passwordhashed;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function cookie()
    {
        return $this->cookie;
    }

    public function columns()
    {
        return $this->columns;
    }

    public function connectcount()
    {
        return $this->connectcount;
    }

    public function expiredate(string $type = 'string')
    {
        if ($type == 'string') {
            if (!empty($this->expiredate)) {
                return $this->expiredate->format('Y-m-d');
            } else {
                return false;
            }
        } elseif ($type == 'date') {
            if (!empty($this->expiredate)) {
                return $this->expiredate;
            } else {
                return false;
            }
        } elseif ($type == 'hrdi') {
            if (empty($this->expiredate)) {
                return 'never';
            } else {
                $now = new DateTimeImmutable("now", timezone_open("Europe/Paris"));
                if ($this->expiredate < $now) {
                    return 'expired';
                } else {
                    return hrdi($this->expiredate->diff($now));
                }
            }
        }
    }

    public function sessions()
    {
        return $this->sessions;
    }


    // _______________________ S E T _______________________

    public function setid($id): bool
    {
        if (is_string($id)) {
            $id = Model::idclean($id);
            if (!empty($id)) {
                $this->id = $id;
                return true;
            }
        }
        return false;
    }

    public function setlevel($level)
    {
        $level = intval($level);
        if ($level >= 0 && $level <= 10) {
            $this->level = $level;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool if password is compatible and set, otherwise flase
     */
    public function setpassword($password): bool
    {
        if (!empty($password) && is_string($password)) {
            if (strlen($password) >= Model::PASSWORD_MIN_LENGTH && strlen($password) <= Model::PASSWORD_MAX_LENGTH) {
                $this->password = $password;
                return true;
            }
        }
        return false;
    }

    public function setsignature(string $signature)
    {
        if (strlen($signature) <= 128) {
            $this->signature = $signature;
        }
    }

    public function setpasswordhashed($passwordhashed)
    {
        $this->passwordhashed = boolval($passwordhashed);
    }

    public function setname($name): void
    {
        if (is_string($name) && strlen($name) < self::LENGTH_SHORT_TEXT) {
            $this->name = strip_tags(trim($name));
        }
    }

    public function seturl($url): void
    {
        if (is_string($url) && strlen($url) < self::LENGTH_SHORT_TEXT) {
            $this->url = strip_tags(trim($url));
        }
    }

    public function setcookie($cookie)
    {
        $cookie = intval($cookie);
        if ($cookie <= Model::MAX_COOKIE_CONSERVATION && $cookie >= 0) {
            $this->cookie = $cookie;
            return true;
        } else {
            return false;
        }
    }

    public function setcolumns($columns)
    {
        if (is_array($columns)) {
            $columns = array_filter(array_intersect(array_unique($columns), self::HOME_COLUMNS));
            $this->columns = $columns;
        }
    }

    public function setconnectcount($connectcount)
    {
        if (is_int($connectcount) && $connectcount >= 0) {
            $this->connectcount = $connectcount;
        }
    }

    public function setexpiredate($expiredate)
    {
        if ($expiredate instanceof DateTimeImmutable) {
            $this->expiredate = $expiredate;
        } else {
            $this->expiredate = DateTimeImmutable::createFromFormat(
                'Y-m-d',
                $expiredate,
                new DateTimeZone('Europe/Paris')
            );
        }
    }

    public function setsessions($sessions)
    {
        if (is_array($sessions)) {
            $this->sessions = $sessions;
        }
    }







    //____________________________________________________ F U N ____________________________________________________






    /**
     * Hash the password and set `$passwordhashed` to true.
     *
     * @return bool true in cas of success, otherwise false.
     */
    public function hashpassword(): bool
    {
        $hashedpassword = password_hash($this->password, PASSWORD_DEFAULT);
        if (!empty($hashedpassword)) {
            $this->password = $hashedpassword;
            $this->passwordhashed = true;
            return true;
        } else {
            return false;
        }
    }

    public function validpassword()
    {
        if (is_string($this->password)) {
            if (
                strlen($this->password) >= Model::PASSWORD_MIN_LENGTH
                && strlen($this->password) <= Model::PASSWORD_MAX_LENGTH
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate new unique session ID and store it
     * @param string $info session info to store
     * @return string session key
     */
    public function newsession(string $info = "no_info"): string
    {
        $exist = true;
        while ($exist === true) {
            $session = bin2hex(randombytes(10));
            $exist = key_exists($session, $this->sessions());
        }
        $this->sessions[$session] = $info;
        return $session;
    }

    /**
     * Remove Session from user
     * @param string $session session ID to remove
     * @return bool true if session exist and was destroyed, false if key does not exist
     */
    public function destroysession(string $session): bool
    {
        if (key_exists($session, $this->sessions)) {
            unset($this->sessions[$session]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if the given session hash identifier is listed in User's open sessions.
     * @param string $session               Hash identifier of the session stored in auth cookie
     */
    public function checksession(string $session): bool
    {
        return key_exists($session, $this->sessions);
    }


    /**
     * Full list of columns with a boolean indicating if checked or not
     *
     * @return bool[]                       associative array where page id is key and value is bool
     */
    public function checkedcolumns(): array
    {
        foreach (self::HOME_COLUMNS as $col) {
            $checkedcolumns[$col] = in_array($col, $this->columns);
        }
        return $checkedcolumns;
    }



    public function isvisitor()
    {
        return $this->level === Modeluser::FREE;
    }

    public function iseditor()
    {
        return $this->level >= Modeluser::EDITOR;
    }

    public function issupereditor()
    {
        return $this->level >= Modeluser::SUPEREDITOR;
    }

    public function isinvite()
    {
        return $this->level >= Modeluser::INVITE;
    }

    public function isadmin()
    {
        return $this->level === Modeluser::ADMIN;
    }


    public function connectcounter()
    {
        $this->connectcount ++;
    }
}
