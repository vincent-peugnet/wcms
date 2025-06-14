<?php

namespace Wcms;

use Error;
use JsonException;

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
     * @return string[]                     Associative array that may contain :
     *                                      `Content-Length` and `Content-Type` key
     *
     * @throws Error                        When `apache_request_headers()` failed
     */
    protected function getrequestheader(): array
    {
        $headers = apache_request_headers();
        if ($headers === false) {
            throw new Error('Failed to read headers');
        } else {
            return $headers;
        }
    }

    /**
     * Send `400` HTTP Error if the JSON decoding failed
     */
    protected function recievejson(): array
    {
        $json = $this->getrequestbody();
        try {
            $datas = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->shortresponse(400, "Json decoding error: " . $e->getMessage());
        }
        return $datas;
    }

    /**
     * Response containing a HTTP header code and a message encoded in JSON
     *
     * @param int $code         HTTP response code header
     * @param string $message   Error message to display
     *
     * @throws void             Indicate to PHPStan that no exception is
     *                          thrown despite the use of `never` return type
     */
    protected function shortresponse(int $code, string $message = ""): never
    {
        http_response_code($code);
        header('Content-type: application/json; charset=utf-8');
        echo json_encode(["message" => $message], JSON_PRETTY_PRINT);
        exit;
    }
}
