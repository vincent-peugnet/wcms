<?php
namespace WFlywheel;

class Predicate extends \JamesMoss\Flywheel\Predicate
{
    public function __construct() {
        $this->operators = array(
            '>', '>=', '<', '<=', '==', '===', '!=', '!==', 'IN'
        );
    }
}
