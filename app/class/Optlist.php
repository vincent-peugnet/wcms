<?php

namespace Wcms;

use DateTimeInterface;
use DOMDocument;
use DOMException;
use IntlDateFormatter;
use LogicException;

class Optlist extends Optcode
{
    protected bool $title = true;
    protected bool $description = false;
    protected bool $thumbnail = false;
    protected bool $date = false;
    protected bool $time = false;
    protected bool $author = false;
    protected bool $hidecurrent = false;
    protected bool $tags = false;
    protected string $style = self::LIST;

    public const LIST = 'list';
    public const CARD = 'card';
    public const STYLES = [
        self::LIST => self::LIST,
        self::CARD => self::CARD,
    ];

    /**
     * @param array<string, mixed> $datas
     */
    public function __construct(array $datas = [])
    {
        parent::__construct($datas);
    }

    /**
     * Get the code to insert directly
     */
    public function getcode(): string
    {
        return '%LIST' . $this->getquery() . '%';
    }

    /**
     * Generate HTML list of links to pages
     *
     * @param Page[] $pagelist              Assoc array of Page objects, key must be ID of page.
     * @param Page $currentpage             Current page
     * @return string HTML formated string
     */
    public function listhtml(array $pagelist, Page $currentpage): string
    {
        if ($this->hidecurrent && key_exists($currentpage->id(), $pagelist)) {
            unset($pagelist[$currentpage->id()]);
        }

        $lang = $currentpage->lang() == '' ? Config::lang() : $currentpage->lang();
        $dateformatter = new IntlDateFormatter($lang, IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
        $datetitleformatter = new IntlDateFormatter($lang, IntlDateFormatter::FULL, IntlDateFormatter::NONE);
        $timeformatter = new IntlDateFormatter($lang, IntlDateFormatter::NONE, IntlDateFormatter::SHORT);

        try {
            $dom = new DOMDocument('1.0', 'UTF-8');

            $ul = $dom->createElement('ul');
            $ul->setAttribute('class', 'pagelist');

            foreach ($pagelist as $page) {
                switch ($this->style) {
                    case self::LIST:
                        $parent = $dom->createElement('li');
                        $a = $dom->createElement('a', htmlspecialchars($page->title()));
                        $a->setAttribute('href', $page->id());
                        $parent->appendChild($a);
                        $ul->appendChild($parent);
                        break;
                    case self::CARD:
                        $li = $dom->createElement('li');
                        $parent = $dom->createElement('a', htmlspecialchars($page->title()));
                        $parent->setAttribute('href', $page->id());
                        $li->appendChild($parent);
                        $ul->appendChild($li);
                        break;
                    default:
                        throw new LogicException('bad LIST style');
                }

                if ($this->description) {
                    $description = $dom->createElement('span', htmlspecialchars($page->description()));
                    $description->setAttribute('class', 'description');
                    $parent->appendChild($description);
                }
                if ($this->date || $this->time) {
                    $values = [];
                    if ($this->date) {
                        $values[] = $dateformatter->format($page->date());
                    }
                    if ($this->time) {
                        $values[] = $timeformatter->format($page->date());
                    }
                    $datetime = $dom->createElement('time', implode(' ', $values));
                    if ($this->date) {
                        $datetime->setAttribute('title', $datetitleformatter->format($page->date()));
                    }
                    $datetime->setAttribute('datetime', $page->date()->format(DateTimeInterface::ATOM));
                    $parent->appendChild($datetime);
                }
                if ($this->author) {
                    $usermanager = new Modeluser();
                    $authors = $dom->createElement('span');
                    $authors->setAttribute('class', 'authors');
                    $users = $usermanager->pageauthors($page);
                    foreach ($users as $user) {
                        $author = $dom->createElement(
                            'a',
                            !empty($user->name()) ? htmlspecialchars($user->name()) : $user->id()
                        );
                        $userclasses = ['user', 'user-' . $user->id()];
                        $author->setAttribute('class', implode(' ', $userclasses));
                        $author->setAttribute('data-user-id', $user->id());
                        if (!empty($user->url())) {
                            $author->setAttribute('href', $user->url());
                        }
                        $authors->appendChild($author);
                    }
                    $parent->appendChild($authors);
                }
                if ($this->thumbnail) {
                    $thumbnail = $dom->createElement('img');
                    if (!empty($page->thumbnail())) {
                        $thumbnail->setAttribute('src', Model::thumbnailpath() . $page->thumbnail());
                    } elseif (!empty(Config::defaultthumbnail())) {
                        $thumbnail->setAttribute('src', Model::thumbnailpath() . Config::defaultthumbnail());
                    }
                    $thumbnail->setAttribute('alt', htmlspecialchars($page->title()));
                    $parent->appendChild($thumbnail);
                }
            }

            $dom->appendChild($ul);

            return $dom->saveHTML($dom->documentElement);
        } catch (DOMException $e) {
            throw new LogicException('bad DOM node used', 0, $e);
        }
    }




    // _______________________________________ G E T _____________________________________


    public function title(): bool
    {
        return $this->title;
    }

    public function description(): bool
    {
        return $this->description;
    }

    public function thumbnail(): bool
    {
        return $this->thumbnail;
    }

    public function date(): bool
    {
        return $this->date;
    }

    public function time(): bool
    {
        return $this->time;
    }

    public function author(): bool
    {
        return $this->author;
    }

    public function hidecurrent(): bool
    {
        return $this->hidecurrent;
    }

    public function style(): string
    {
        return $this->style;
    }


    // _______________________________________ S E T _____________________________________

    public function settitle(bool $title): void
    {
        $this->title = $title;
    }

    public function setdescription(bool $description): void
    {
        $this->description = $description;
    }

    public function setthumbnail(bool $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }

    public function setdate(bool $date): void
    {
        $this->date = $date;
    }

    public function settime(bool $time): void
    {
        $this->time = $time;
    }

    public function setauthor(bool $author): void
    {
        $this->author = $author;
    }

    public function sethidecurrent(bool $hidecurrent): void
    {
        $this->hidecurrent = $hidecurrent;
    }

    public function setstyle(string $style): void
    {
        if (key_exists($style, self::STYLES)) {
            $this->style = $style;
        }
    }
}
