<?php

namespace Wcms;

/**
 * Simple "c struct like" class that represent a Header's datas.
 * All members are public because there is no logic in this class. It is only
 * used to pass data from one element to another with cleanly typed members.
 */
class Header
{
    /** @var string $id the id of this header. */
    public $id;
    /** @var int $level the level of deepness of this header. */
    public $level;
    /** @var string $title the title displayed by this header. */
    public $title;

    public function __construct(string $id, int $level, string $title)
    {
        $this->id = $id;
        $this->level = $level;
        $this->title = $title;
    }
}
