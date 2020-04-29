<?php

namespace Wcms;

use DateTimeImmutable;
use DateTimeZone;
use RuntimeException;

class User extends Item
{
    protected $id = '';
    protected $level = 0;
    protected $signature = '';
    protected $password;
    protected $passwordhashed = false;
    protected $cookie = 0;
    protected $columns = ['title', 'datemodif', 'datecreation', 'secure', 'visitcount'];
    protected $connectcount = 0;
    protected $expiredate = false;
    /** @var Bookmark[] Associative array as `id => Bookmark`*/
    protected $bookmark = [];
    protected $display = ['bookmark' => false];

    public function __construct($datas = [])
    {
        if (!empty($datas)) {
            $this->hydrate($datas);
        }
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

    public function bookmark()
    {
        return $this->bookmark;
    }

    public function display()
    {
        return $this->display;
    }


    // _______________________ S E T _______________________

    public function setid($id): bool
    {
        if (is_string($id)) {
            $id = idclean($id);
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

    public function setpassword($password)
    {
        if (!empty($password) && is_string($password)) {
            if (strlen($password) >= Model::PASSWORD_MIN_LENGTH && strlen($password) <= Model::PASSWORD_MAX_LENGTH) {
                $this->password = $password;
                return true;
            } else {
                return false;
            }
        }
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
            $columns = array_filter(array_intersect(array_unique($columns), Model::COLUMNS));
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

    public function setbookmark($bookmark)
    {
        if (is_array($bookmark)) {
            $bookmark = array_map(
                function ($datas) {
                    try {
                        return new Bookmark($datas);
                    } catch (RuntimeException $e) {
                        return false;
                    }
                },
                $bookmark
            );
            $this->bookmark = array_filter($bookmark);
        }
    }

    public function setdisplay($display)
    {
        if (is_array($display)) {
            $this->display = $display;
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

    public function addbookmark(Bookmark $bookmark)
    {
        if (!empty($bookmark->id()) && !empty($bookmark->query()) && !empty($bookmark->route())) {
            $this->bookmark[$bookmark->id()] = $bookmark;
        }
    }

    public function deletebookmark(string $id)
    {
        if (key_exists($id, $this->bookmark)) {
            unset($this->bookmark[$id]);
        }
    }
}
