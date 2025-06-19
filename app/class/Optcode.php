<?php

namespace Wcms;

class Optcode extends Opt
{
    /** @var string $bookmark Associated bookmark ID */
    protected string $bookmark = "";

    public function bookmark(): string
    {
        return $this->bookmark;
    }

    /**
     * @param string $bookmark              Bookmark ID
     */
    public function setbookmark(string $bookmark): void
    {
        if (Model::idcheck($bookmark)) {
            $this->bookmark = $bookmark;
        }
    }
}
