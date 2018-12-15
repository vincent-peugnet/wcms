<?php
namespace WFlywheel;

class Query extends \JamesMoss\Flywheel\Query
{
    /**
     * Constructor
     *
     * @param Repository $repository The repo this query will run against.
     */
    public function __construct(Repository $repository)
    {
        parent::__construct($repository);
        $this->predicate = new Predicate();
    }
}
