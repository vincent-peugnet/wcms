<?php

namespace Wcms;

use JamesMoss\Flywheel\Document;
use Wcms\Exception\Database\Notfoundexception;
use Wcms\Exception\Databaseexception;

class Modelcomment extends Modeldb
{
    public const COMMENT_REPO_NAME = 'comment';

    /** Name of POST index that store the comment config as a JWT */
    public const CONFIG_POST_NAME = 'wcms-comment-form-configuration';

    public function __construct()
    {
        parent::__construct();
        $this->storeinit(self::COMMENT_REPO_NAME);
    }

    /**
     * @return array<string, Comment>       Comments sorted by 'most recents'
     */
    public function list(): array
    {
        $comments = [];
        $pagecommentsdata = $this->repo->findAll();
        foreach ($pagecommentsdata as $pagecommentdata) {
            $page = $pagecommentdata->getId();
            foreach ($pagecommentdata as $id => $data) {
                $com = Comment::new($data);
                $comments[implode('#', [$page, $id])] = $com;
            }
        }
        $this->sort($comments, 'date', -1);
        return $comments;
    }

    /**
     * Add a new comment to a specified page.
     * If it's the first, it will create a new entry in comment database.
     *
     * @return int                          total count of comment
     *
     * @throws Databaseexception            if something failed
     */
    public function addcomment(Page $page, Comment $comment): int
    {
        try {
            $data = $this->get($page->id());
        } catch (Notfoundexception $e) { // if the document does not exist yet
            $data = $this->add($page->id());
        }

        $lastid = array_key_last($data);
        if ($lastid !== null) {
            $id = intval($lastid) + 1;
        } else {
            $id = 1;
        }
        $data[$id] = $comment->dry();

        $doc = new Document($data);
        $doc->setId($page->id());
        $this->updatedoc($doc);

        return count($data);
    }

    /**
     * @param string $id                    Page ID
     *
     * @return array<int, Comment>
     *
     * @throws Databaseexception            If ID is not valid
     * @throws Notfoundexception            If comments cannot be found for the given page ID
     */
    public function getcomments(string $id): array
    {
        $data = $this->get($id);
        $comments = [];
        foreach ($data as $id => $data) {
            $comments[intval($id)] = Comment::new($data);
        }
        return $comments;
    }

    /**
     * Add an empty document to the database with given page ID
     *
     * @param string $id                    Page ID
     *
     * @return array<int, array<string, string>>
     *
     * @throws Databaseexception            if an error occured during storing
     */
    protected function add(string $id): array
    {
        $doc = new Document([]);
        $doc->setId($id);
        $this->storedoc($doc);
        return [];
    }

    /**
     * @param string $id                    Page ID
     *
     * @return array<string, mixed>
     *
     * @throws Databaseexception            If ID is not valid
     * @throws Notfoundexception            If comments cannot be found
     */
    protected function get(string $id): array
    {
        if (!$this->idcheck($id)) {
            throw new Databaseexception("invalid ID: '$id'");
        }
        $doc = $this->repo->findById($id);
        if ($doc === false) {
            throw new Notfoundexception("Could not find page comments entry with the following ID: '$id'");
        }
        return get_object_vars($doc);
    }

    /**
     * delete page comments from database if it exists
     *
     * @throws Databaseexception            if deletion failed
     */
    public function delete(string $id): void
    {
        try {
            $this->get($id);
            if (!$this->repo->delete($id)) {
                throw new Databaseexception("deleting comments for page '$id' failed");
            }
        } catch (Notfoundexception $e) {
            // no need to delete it if it does not exist
        }
    }

    /**
     * @param array<int, Comment> $comments
     *
     * @throws Databaseexception if update failed
     */
    protected function update(string $id, array $comments): void
    {
        array_walk($comments, function (Comment &$comment) {
            $comment = $comment->dry();
        });
        $doc = new Document($comments);
        $doc->setId($id);
        $this->updatedoc($doc);
    }

    /**
     * @param array<int, string> $statuses     key is comment ID, value is `-1`, `0` or `1`
     *
     * @throws Databaseexception if no comment are found for given page ID or update failed
     */
    public function applymoderation(string $pageid, array $statuses): void
    {
        $comments = $this->getcomments($pageid);

        foreach ($comments as $id => $comment) {
            if (!isset($statuses[$id])) {
                continue; // this is strange
            }
            $status = intval($statuses[$id]);
            if ($status === -1) {
                unset($comments[$id]);
            } else {
                $comments[$id]->setapproved(boolval($status));
            }
        }
        $this->update($pageid, $comments);
    }

    /**
     * Sort an array of Comments
     *
     * @param Comment[] $comments
     * @param string $sortby
     * @param int $order                    Can be 1 or -1
     */
    protected function sort(array &$comments, string $sortby = 'date', int $order = 1): void
    {
        $sortby = (in_array($sortby, ['date'])) ? $sortby : 'date';
        $order = ($order === 1 || $order === -1) ? $order : 1;
        uasort($comments, $this->buildsorter($sortby, $order));
    }

    protected function buildsorter(string $sortby, int $order): callable
    {
        return function (Comment $comment1, Comment $comment2) use ($sortby, $order) {
            $result = $this->compare($comment1, $comment2, $sortby, $order);
            return $result;
        };
    }

    protected function compare(Comment $comment1, Comment $comment2, string $property = 'date', int $order = 1): int
    {
        $result = ($comment1->$property() <=> $comment2->$property());
        return $result * $order;
    }
}
