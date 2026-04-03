<?php

namespace Wcms;

use DateTimeInterface;
use DOMDocument;
use DOMException;
use DOMNode;
use IntlDateFormatter;
use LogicException;
use RuntimeException;
use VStelmakh\UrlHighlight\UrlHighlight;

class Comments extends Item
{
    protected int $order = 1;
    protected ?string $id = null;
    protected ?int $limit = null;

    /** Show unapproved comments */
    protected bool $unapproved = false;

    /** @var Page $page the rendered page */
    protected Page $page;

    protected Modelcomment $commentmanager;
    protected Modeluser $usermanager;

    protected IntlDateFormatter $datedisplayformater;
    protected IntlDateFormatter $datetitleformatter;

    protected UrlHighlight $urlhighlighter;

    /**
     * @param Page $page                    Page where the comment list is rendered
     * @param array<string, mixed> $data
     */
    public function __construct(Page $page, array $data = [])
    {
        $this->page = $page;
        $this->hydrate($data);

        $lang = $this->page->lang() == '' ? Config::lang() : $this->page->lang();
        $this->datedisplayformater = new IntlDateFormatter($lang, IntlDateFormatter::SHORT, IntlDateFormatter::MEDIUM);
        $this->datetitleformatter = new IntlDateFormatter($lang, IntlDateFormatter::FULL, IntlDateFormatter::NONE);

        $this->commentmanager = new Modelcomment();
        $this->usermanager = new Modeluser();
        $this->urlhighlighter = new UrlHighlight();
    }

    /**
     * @throws RuntimeException if ID param failed (page do not exist or database error)
     */
    public function listhtml(): string
    {
        try {
            if ($this->id !== null) {
                $pagemanager = new Modelpage(Config::pagetable());
                $commentpage = $pagemanager->get($this->id);
            } else {
                $commentpage = $this->page;
            }

            if ($commentpage->commentcount() === 0) {
                $comments = [];
            } else {
                $comments = $this->commentmanager->getcomments($commentpage->id());
            }
        } catch (RuntimeException $e) {
            throw new RuntimeException("comments inclusion: ", 0, $e);
        }

        if ($this->order === -1) {
            $comments = array_reverse($comments, true);
        }

        try {
            $dom = new DOMDocument('1.0', 'UTF-8');

            $ul = $dom->createElement('ul');
            $ul->setAttribute('class', 'comments');

            $i = 0;
            foreach ($comments as $id => $comment) {
                if ((!$this->unapproved && !$comment->approved()) || $i === $this->limit) {
                    continue;
                }
                $li = $this->commentline($id, $comment, $dom);
                $ul->appendChild($li);
                $i++;
            }

            $dom->appendChild($ul);
            return $dom->saveHTML($dom->documentElement);
        } catch (DOMException $e) {
            throw new LogicException('bad DOM node used', 0, $e);
        }
    }

    /**
     * Render a comment line as a `li` HTML DOM node
     *
     * @throws DOMException                 in case of DOM error
     */
    protected function commentline(int $id, Comment $comment, DOMDocument $dom): DOMNode
    {
        $li = $dom->createElement('li');
        $fragment = "comment-$id";
        $li->setAttribute('id', $fragment);

        $classes = ['comment'];
        $classes[] = $comment->approved() ? 'approved' : 'unapproved';
        $li->setAttribute('class', implode(' ', $classes));

        $li->setAttribute('data-approved', strval(intval($comment->approved())));
        $fragmentlink = $dom->createElement('a', "#$id");
        $fragmentlink->setAttribute('href', "#$fragment");
        $fragmentlink->setAttribute('class', 'comment-id'); // TODO: find a good class name
        $li->appendChild($fragmentlink);

        if ($comment instanceof Commentuser) {
            try {
                $user = $this->usermanager->get($comment->username());
                $userlink = $dom->createElement('a', empty($user->name()) ? $user->id() : $user->name());
                if (!empty($user->url())) {
                    $userlink->setAttribute('href', $user->url());
                }
            } catch (RuntimeException $e) {
                $userlink = $dom->createElement('a', $comment->username());
            }
            $userlink->setAttribute('class', 'user');
            $li->appendChild($userlink);
        } elseif ($comment instanceof Commentvisitor && !empty($comment->pseudonym())) {
            $userlink = $dom->createElement('a', htmlspecialchars($comment->pseudonym()));
            $userlink->setAttribute('class', 'visitor');
            if (!empty($comment->website())) {
                $userlink->setAttribute('href', $comment->website());
            }
            $li->appendChild($userlink);
        }


        $time = $dom->createElement('time', $this->datedisplayformater->format($comment->date()));
        $time->setAttribute('datetime', $comment->date()->format(DateTimeInterface::ATOM));
        $time->setAttribute('title', $this->datetitleformatter->format($comment->date()));
        $li->appendChild($time);

        $message = $dom->createDocumentFragment();
        $message->appendXML(
            $this->urlhighlighter->highlightUrls(nl2br(htmlspecialchars($comment->message())))
        );

        $paragraph = $dom->createElement('p');
        $paragraph->appendChild($message);
        $paragraph->setAttribute('class', 'message');
        $li->appendChild($paragraph);

        return $li;
    }

    public function order(): int
    {
        return $this->order;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    public function unapproved(): bool
    {
        return $this->unapproved;
    }

    /**
     * @param mixed $order
     */
    public function setorder($order): void
    {
        $order = intval($order);
        if ($order == 1 || $order == -1) {
            $this->order = $order;
        }
    }

    public function setid(string $id): void
    {
        $this->id = $id;
    }

    public function setlimit(?int $limit): void
    {
        if ($limit === null) {
            $this->limit = null;
            return;
        }
        if ($limit >= 0) {
            $this->limit = $limit;
        }
    }

    public function setunapproved(bool $unapproved): void
    {
        $this->unapproved = $unapproved;
    }
}
