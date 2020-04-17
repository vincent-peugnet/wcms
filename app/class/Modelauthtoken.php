<?php

namespace Wcms;

use DateTimeImmutable;
use JamesMoss\Flywheel\Document;

class Modelauthtoken extends Modeldb
{

    protected const AUTHTOKEN_REPO_NAME = 'authtoken';
    protected const AUTHTOKEN_ID_LENGTH = 30;

    public function __construct()
    {
        parent::__construct();
        $this->storeinit(self::AUTHTOKEN_REPO_NAME);
    }

    /**
     * Add a Token in the database according to the Users datas
     *
     * @param User $user
     */
    public function add(User $user)
    {
        $datas = [
            'user' => $user->id(),
            'ip' => $_SERVER['SERVER_ADDR'],
            'date' => new DateTimeImmutable(),
            'conservation' => $user->cookie(),
            'useragent' => $_SERVER['HTTP_USER_AGENT']
        ];
        $tokendata = new Document($datas);

        $exist = true;
        while ($exist !== false) {
            $id = bin2hex(random_bytes(self::AUTHTOKEN_ID_LENGTH));
            $exist = $this->repo->findById($id);
        }

        $tokendata->setId($id);
        return $this->repo->store($tokendata);
    }

    public function getbytoken(string $token)
    {
        return $this->repo->findById($token);
    }

    public function delete(string $token)
    {
        return $this->repo->delete($token);
    }

    /**
     * @param string $id user Id
     */
    public function listbyuser(string $id)
    {
        return $this->repo->query()->where('user', '==', $id)->orderBy('date')->execute();
    }
}
