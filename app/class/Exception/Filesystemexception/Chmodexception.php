<?php

namespace Wcms\Exception\Filesystemexception;

use Wcms\Exception\Filesystemexception;

class Chmodexception extends Filesystemexception
{
    public function __construct(
        string $message,
        int $permissions,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        $message .= sprintf(' permissions: %o', $permissions);
        parent::__construct($message, $code, $previous);
    }
}
