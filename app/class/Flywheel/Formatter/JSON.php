<?php

namespace Wcms\Flywheel\Formatter;

use JsonException;

class JSON implements \JamesMoss\Flywheel\Formatter\FormatInterface
{
    public function getFileExtension()
    {
        return 'json';
    }

    /**
     * @throws JsonException if json_encode fails in PHP7.3
     * @phpstan-ignore-next-line
     */
    public function encode(array $data)
    {
        $options = defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE : null;
        return json_encode($data, $options);
    }
    public function decode($data)
    {
        return json_decode($data, true);
    }
}
