<?php

namespace Wcms\Flywheel\Formatter;

class JSON implements \JamesMoss\Flywheel\Formatter\FormatInterface
{
    public function getFileExtension()
    {
        return 'json';
    }
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
