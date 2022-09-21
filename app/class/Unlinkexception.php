<?php

namespace Wcms;

use Throwable;

/**
 * Error while trying to unlink file on the filesystem
 */
class Unlinkexception extends Filesystemexception
{
    /**
     * @param string $file                  Filename that was'nt found
     * @param int $code                     [optional] The Exception code.
     * @param ?Throwable $previous          [optional] The previous throwable used for the exception chaining.
     */
    public function __construct(
        string $file,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $message = "Error while trying to delete file: $file";
        parent::__construct($message, $code, $previous);
    }
}
