<?php

namespace Wcms;

use Wcms\Exception\Database\Notfoundexception;
use Wcms\Exception\Databaseexception;

class Controllerapiuser extends Controllerapi
{
    /**
     * Retrieve an user from the database. For admins only
     *
     * - Send `401` If user is unauthorized
     * - Send `404` If user is not found
     */
    public function get(string $user): void
    {
        if (!$this->user->isadmin()) {
            $this->shortresponse(401, 'Access unauthrozed, you need to be admin');
        }
        try {
            $user = $this->usermanager->get($user);
            http_response_code(200);
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($user, JSON_PRETTY_PRINT);
        } catch (Databaseexception $e) {
            $this->shortresponse(404, "User not found: $e");
        }
    }
}
