<?php

namespace Wcms;

class Search
{
    public string $query = '';

    public bool $casesensitive = false;

    public bool $id = true;
    public bool $title = true;
    public bool $description = true;
    public bool $content = true;
    public bool $other = false;

    /**
     * @param array<string, mixed> $params
     */
    public function __construct(array $params = [])
    {
        if (!isset($params['query'])) {
            return;
        }

        $this->query = $params['query'];

        $this->id = $params['id'] ?? false;
        $this->title = $params['title'] ?? false;
        $this->description = $params['description'] ?? false;
        $this->content = $params['content'] ?? false;
        $this->other = $params['other'] ?? false;

        if (
            $this->id === false &&
            $this->title === false &&
            $this->description === false &&
            $this->content === false &&
            $this->other === false
        ) {
            $this->id = true;
            $this->title = true;
            $this->description = true;
            $this->content = true;
            $this->other = true;
        }

        $this->casesensitive = $params['casesensitive'] ?? false;
    }

    public function isactive(): bool
    {
        return !empty($this->query);
    }
}
