<?php

namespace Wcms;

use DateTimeImmutable;
use DateTimeZone;
use DomainException;

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

    /** @var array<string, string> sessions as hash => name */
    protected array $sessions = [];

    protected string $theme = '';

    public const LEVELS = [
        1  => 'reader',
        2  => 'invite editor',
        3  => 'editor',
        4  => 'super editor',
        10 => 'admin',
    ];

    public const HOME_COLUMNS = [
        'favicon',
        'download',
        'tag',
        'title',
        'description',
        'linkto',
        'externallinks',
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

    /**
     * @param array<string, mixed>|object $data
     */
    public function __construct($data = [])
    {
        $this->hydrate($data);
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

    public function id(): string
    {
        return $this->id;
    }

    public function level(): int
    {
        return $this->level;
    }

    public function password(): ?string
    {
        return $this->password;
    }

    public function signature(): string
    {
        return $this->signature;
    }

    public function passwordhashed(): bool
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

    public function cookie(): int
    {
        return $this->cookie;
    }

    /**
     * @return string[]
     */
    public function columns(): array
    {
        return $this->columns;
    }

    public function connectcount(): int
    {
        return $this->connectcount;
    }

    /**
     * @throws DomainException if provided type is invalid
     *
     * @return false|string|DateTimeImmutable
     */
    public function expiredate(string $type = 'date')
    {
        if ($type === 'string') {
            if (!empty($this->expiredate)) {
                return $this->expiredate->format('Y-m-d');
            } else {
                return false;
            }
        } elseif ($type === 'date') {
            if (!empty($this->expiredate)) {
                return $this->expiredate;
            } else {
                return false;
            }
        } elseif ($type === 'hrdi') {
            if (empty($this->expiredate)) {
                return 'never';
            } else {
                $now = new DateTimeImmutable("now", timezone_open("Europe/Paris"));
                if ($this->expiredate < $now) {
                    return 'expired';
                } else {
                    return 'in ' . hrdi($this->expiredate->diff($now));
                }
            }
        }
        throw new DomainException("'$type' is not a valid type");
    }

    /**
     * @return string[]
     */
    public function sessions(): array
    {
        return $this->sessions;
    }

    public function theme(): string
    {
        return $this->theme;
    }


    // _______________________ S E T _______________________

    public function setid(string $id): bool
    {
        $id = Model::idclean($id);
        if (!empty($id)) {
            $this->id = $id;
            return true;
        }
        return false;
    }

    public function setlevel(int $level): bool
    {
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
    public function setpassword(?string $password): bool
    {
        if (
            is_string($password) &&
            strlen($password) < Model::PASSWORD_MIN_LENGTH ||
            strlen($password) > Model::PASSWORD_MAX_LENGTH
        ) {
            return false;
        }
        $this->password = $password;
        return true;
    }

    public function setsignature(string $signature): void
    {
        if (strlen($signature) <= 128) {
            $this->signature = $signature;
        }
    }

    public function setpasswordhashed(bool $passwordhashed): void
    {
        $this->passwordhashed = $passwordhashed;
    }

    public function setname(string $name): void
    {
        if (strlen($name) < self::LENGTH_SHORT_TEXT) {
            $this->name = strip_tags(trim($name));
        }
    }

    public function seturl(string $url): void
    {
        if (strlen($url) < self::LENGTH_SHORT_TEXT) {
            $this->url = strip_tags(trim($url));
        }
    }

    public function setcookie(int $cookie): bool
    {
        if ($cookie <= Model::MAX_COOKIE_CONSERVATION && $cookie >= 0) {
            $this->cookie = $cookie;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string[] $columns
     */
    public function setcolumns(array $columns): void
    {
        $columns = array_filter(array_intersect(array_unique($columns), self::HOME_COLUMNS));
        $this->columns = $columns;
    }

    public function setconnectcount(int $connectcount): void
    {
        if ($connectcount >= 0) {
            $this->connectcount = $connectcount;
        }
    }

    /**
     * @param DateTimeImmutable|string|false $expiredate accepted string format is `Y-m-d`
     */
    public function setexpiredate($expiredate): void
    {
        if ($expiredate instanceof DateTimeImmutable || $expiredate === false) {
            $this->expiredate = $expiredate;
        } else {
            $this->expiredate = DateTimeImmutable::createFromFormat(
                'Y-m-d',
                $expiredate,
                new DateTimeZone('Europe/Paris')
            );
        }
    }

    /**
     * @param string[] $sessions
     */
    public function setsessions(array $sessions): void
    {
        $this->sessions = $sessions;
    }

    public function settheme(string $theme): void
    {
        $this->theme = $theme;
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


    /**
     * User is just a visitor
     */
    public function isvisitor(): bool
    {
        return $this->level === Modeluser::FREE;
    }

    /**
     * User is at least invite editor
     */
    public function isinvite(): bool
    {
        return $this->level >= Modeluser::INVITE;
    }

    /**
     * User is at least editor
     */
    public function iseditor(): bool
    {
        return $this->level >= Modeluser::EDITOR;
    }

    /**
     * User is at least super editor
     */
    public function issupereditor(): bool
    {
        return $this->level >= Modeluser::SUPEREDITOR;
    }

    /**
     * User is admin
     */
    public function isadmin(): bool
    {
        return $this->level === Modeluser::ADMIN;
    }


    public function connectcounter(): void
    {
        $this->connectcount ++;
    }
}
