<?php

namespace Wcms;

use Throwable;

/**
 * File not found
 */
class Notfoundexception extends Filesystemexception
{
    /**
     * @param string $file                  Filename that was'nt found
     * @param int $code                     [optional] The Exception code.
     * @param ?Throwable $previous         [optional] The previous throwable used for the exception chaining.
     */
    public function __construct(
        string $file,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $message = "File: $file not found";
        parent::__construct($message, $code, $previous);
    }
}
