<?php

namespace Wcms\Flywheel\Formatter;

/**
 * Custom JSON formater that use Pretty Print option if available.
 */
class JSON implements \JamesMoss\Flywheel\Formatter\FormatInterface
{
    public function getFileExtension(): string
    {
        return 'json';
    }

    /**
     * @param mixed[] $data
     * @return string|false
     */
    public function encode(array $data)
    {
        $options = defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE : null;
        return json_encode($data, $options);
    }

    /**
     * @param string $data
     * @return true|false|null|mixed[]
     */
    public function decode($data)
    {
        return json_decode($data, true);
    }
}
