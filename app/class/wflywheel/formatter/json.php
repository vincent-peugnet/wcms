<?php
namespace WFlywheel\Formatter;

class JSON implements \JamesMoss\Flywheel\Formatter\FormatInterface
{
    public function getFileExtension()
    {
        return 'json';
    }
    public function encode(array $data)
    {
        $options = defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : null;
        $options .= JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;
        return json_encode($data, $options);
    }
    public function decode($data)
    {
        $options = defined('JSON_OBJECT_AS_ARRAY') ? JSON_OBJECT_AS_ARRAY : null;
        return json_decode($data, $options);
    }
}