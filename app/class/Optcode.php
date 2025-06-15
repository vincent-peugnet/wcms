<?php

namespace Wcms;

class Optcode extends Opt
{
    /** @var string $bookmark Associated bookmark ID */
    protected string $bookmark = "";

    /**
     * Parse datas from HTTP encoded params, then hydrate the object.
     * This method also transform `*` into current page.
     *
     * @param string $encoded               Options encoded as HTTP params. May start with a `?` or not.
     * @param Page $currentpage             Used to substitute wildcard `*` with Page id.
     */
    public function parsehydrate(string $encoded, Page $currentpage)
    {
        $encoded = str_replace('*', $currentpage->id(), $encoded);
        parse_str(ltrim($encoded, "?"), $datas);
        $this->hydrate($datas);
    }

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
