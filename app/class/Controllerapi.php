<?php

namespace Wcms;

use Error;

class Controllerapi extends Controller
{
    /**
     * @throws Error in case of stream_get_contents failure
     *
     * @return string the content of the request's body
     */
    protected function getrequestbody(): string
    {
        $body = file_get_contents('php://input');
        if ($body === false) {
            throw new Error("failed to read STDIN stream");
        }
        return $body;
    }

    /**
     * Response containing a HTTP header code and a message encoded in JSON
     *
     * @param int $code         HTTP response code header
     * @param string $message   Error message to display
     */
    protected function shortresponse(int $code, string $message = ""): never
    {
        http_response_code($code);
        header('Content-type: application/json; charset=utf-8');
        echo json_encode(["message" => $message], JSON_PRETTY_PRINT);
        exit;
    }
}
