<?php

namespace Wcms;

use RuntimeException;

class Controllerapiconnect extends Controllerapi
{
    protected function setuser(): bool
    {
        return false;
    }

    public function auth(): never
    {
        $data = $this->recievejson();
        if (!isset($data['username']) || !isset($data['password'])) {
            $this->shortresponse(400, "JSON should have 'username' and 'password' keys");
        }
        $username = strval($data['username']);
        $password = strval($data['password']);
        try {
            $user = $this->connectmanager->login($username, $password);
            $msg = date('Y-m-d') . '/' . $_SERVER['HTTP_USER_AGENT'];
            $wsessionid = $user->newsession($msg);
            $jwt = $this->connectmanager->createjwt($user->id(), $wsessionid);
            $usermanager = new Modeluser();
            $usermanager->add($user);
            http_response_code(200);
            header('Content-type: application/json; charset=utf-8');
            echo json_encode(['token' => $jwt], JSON_PRETTY_PRINT);
            Logger::info("API auth token generated for user $username");
            exit;
        } catch (RuntimeException $e) {
            $this->shortresponse(400, $e->getMessage());
        }
    }

    public function health(): never
    {
        $this->shortresponse(200, "W is healthy");
    }
}
