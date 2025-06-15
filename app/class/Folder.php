<?php

namespace Wcms;

class Folder
{
    public string $name;

    /** @var Folder[] */
    public array $childs;
    public int $filecount;
    public string $path;
    public int $deepness;
    public bool $open = false;
    public bool $selected = false;

    public function __construct(string $name, array $childs, int $filecount, string $path, int $deepness)
    {
        $this->name = $name;
        $this->childs = $childs;
        $this->filecount = $filecount;
        $this->path = $path;
        $this->deepness = $deepness;
    }
}
