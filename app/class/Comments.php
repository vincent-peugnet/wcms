<?php

namespace Wcms;

use DateTimeInterface;
use DOMDocument;
use DOMException;
use IntlDateFormatter;
use LogicException;
use RuntimeException;

class Comments extends Item
{
    protected int $order = 1;
    protected string $id = '';

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->hydrate($data);
    }

    /**
     * @throws RuntimeException if ID param failed (page do not exist or database error)
     */
    public function listhtml(Page $page): string
    {
        $lang = $page->lang() == '' ? Config::lang() : $page->lang();
        $datedisplayformater = new IntlDateFormatter($lang, IntlDateFormatter::SHORT, IntlDateFormatter::MEDIUM);
        $datetitleformatter = new IntlDateFormatter($lang, IntlDateFormatter::FULL, IntlDateFormatter::NONE);

        $usermanager = new Modeluser();


        if (!empty($this->id)) {
            $pagemanager = new Modelpage(Config::pagetable());
            try {
                $commentspage = $pagemanager->get($this->id);
                $comments = $commentspage->comments();
            } catch (RuntimeException $e) {
                throw new RuntimeException("comments inclusion: ", 0, $e);
            }
        } else {
            $comments = $page->comments();
        }


        if ($this->order === -1) {
            $comments = array_reverse($comments, true);
        }

        try {
            $dom = new DOMDocument('1.0', 'UTF-8');

            $ul = $dom->createElement('ul');
            $ul->setAttribute('class', 'comments');

            foreach ($comments as $id => $comment) {
                $li = $dom->createElement('li');
                $fragment = "comment-$id";
                $li->setAttribute('id', $fragment);
                $li->setAttribute('class', 'comment');
                $fragmentlink = $dom->createElement('a', "#$id");
                $fragmentlink->setAttribute('href', "#$fragment");
                $fragmentlink->setAttribute('class', 'comment-id'); // TODO: find a good class name


                try {
                    $user = $usermanager->get($comment->username());
                    $userlink = $dom->createElement('a', $user->name());
                    if (!empty($user->url())) {
                        $userlink->setAttribute('href', $user->url());
                    }
                } catch (RuntimeException $e) {
                    $userlink = $dom->createElement('a', $comment->username());
                }

                $userlink->setAttribute('class', 'user');

                $time = $dom->createElement('time', $datedisplayformater->format($comment->date()));
                $time->setAttribute('datetime', $comment->date()->format(DateTimeInterface::ATOM));
                $time->setAttribute('title', $datetitleformatter->format($comment->date()));

                $message = $dom->createElement('p', $comment->message());
                $message->setAttribute('class', 'message');

                $li->appendChild($fragmentlink);
                $li->appendChild($userlink);
                $li->appendChild($time);
                $li->appendChild($message);

                $ul->appendChild($li);
            }

            $dom->appendChild($ul);
            return $dom->saveHTML($dom->documentElement);
        } catch (DOMException $e) {
            throw new LogicException('bad DOM node used', 0, $e);
        }
    }

    public function order(): int
    {
        return $this->order;
    }

    public function id(): string
    {
        return $this->id;
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
}
