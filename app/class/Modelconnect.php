<?php

namespace Wcms;

use Firebase\JWT\JWT;
use RuntimeException;
use Exception;

class Modelconnect extends Model
{
    /**
     * @param string $userid
     * @param string $wsession
     * @param int $conservation
     * @throws RuntimeException if secret key is not set or cant send cookie
     */
    public function createauthcookie(string $userid, string $wsession, int $conservation)
    {
        $datas = [
            "userid" => $userid,
            "wsession" => $wsession
        ];
        if (empty(Config::secretkey())) {
            throw new RuntimeException("Secret Key not set");
        }
        $jwt = JWT::encode($datas, Config::secretkey());
        $cookie = setcookie('authtoken', $jwt, time() + $conservation * 24 * 3600, "", "", false, true);
        if (!$cookie) {
            throw new RuntimeException("Cant be send");
        }
    }

    /**
     * Check cookie using JWT
     * @throws Exception
     */
    public function checkcookie()
    {
        if (!empty($_COOKIE['authtoken'])) {
            $datas = JWT::decode($_COOKIE['authtoken'], Config::secretkey(), ['HS256']);
            return get_object_vars($datas);
        }
    }
}
