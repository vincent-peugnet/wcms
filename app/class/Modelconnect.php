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
        $options = [
            'expires' => time() + $conservation * 24 * 3600,
            'path' => '/' . Config::basepath(),
            'domain' => '',
            'secure' => Config::issecure(),
            'httponly' => true,
            'samesite' => 'Strict'
        ];
        $cookie = setcookie('rememberme', $jwt, $options);
        if (!$cookie) {
            throw new RuntimeException("Remember me cookie cannot be created");
        }
    }

    /**
     * Get decoded cookie using JWT
     * @return array                        Associative array containing JWT token's datas
     * @throws RuntimeException             If JWT token decode failed or auth cookie is unset
     */
    public function checkcookie(): array
    {
        if (!empty($_COOKIE['rememberme'])) {
            $datas = JWT::decode($_COOKIE['rememberme'], Config::secretkey(), ['HS256']);
            return get_object_vars($datas);
        } else {
            throw new RuntimeException('Auth cookie is unset');
        }
    }

    /**
     * Delete authentication cookie
     */
    public function deleteauthcookie(): void
    {
        $_COOKIE['rememberme'] = [];
    }
}
