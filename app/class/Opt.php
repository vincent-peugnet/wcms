<?php

namespace Wcms;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

class Opt extends Item
{
    protected string $sortby = 'id';
    protected int $order = 1;

    /** @var string[] $tagfilter    List of tags as string */
    protected array $tagfilter = [];

    /** @var string $tagcompare     Can be OR, AND or EMPTY */
    protected string $tagcompare = 'AND';

    /**  */
    protected bool $tagnot = false;

    /** @var string[] $authorfilter */
    protected array $authorfilter = [];
    protected string $authorcompare = 'AND';
    protected int $secure = 4;
    protected string $linkto = '';

    /** @var array<string, int> $taglist */
    protected array $taglist = [];

    /** @var  array<string, int> $authorlist */
    protected array $authorlist = [];
    protected bool $invert = false;
    protected int $limit = 0;

    /** @var DateTimeImmutable $since */
    protected ?DateTimeImmutable $since = null;

    /** @var DateTimeImmutable $until */
    protected ?DateTimeImmutable $until = null;

    protected bool $geo = false;

    protected int $version = 0;

    /** @var string[] $pageidlist */
    protected $pageidlist = [];

    /** @var string[] $pagevarlist List fo every properties of an Page object */
    protected array $pagevarlist = [];

    public const OR             = 'OR';
    public const AND            = 'AND';
    public const EMPTY          = 'EMPTY';
    public const COMPARE    = [self::OR, self::AND, self::EMPTY];

    protected const SORTLIST = [
        'sortby',
        'order',
    ];

    protected const FILTERLIST = [
        'secure',
        'tagfilter',
        'tagcompare',
        'tagnot',
        'authorfilter',
        'authorcompare',
        'linkto',
        'since',
        'until',
        'geo',
        'version',
        'invert',
        'limit'
    ];

    protected const DATALIST = [...self::SORTLIST, ...self::FILTERLIST];

    public const SORTBYLIST = [
        'favicon',
        'id',
        'tag',
        'title',
        'linkto',
        'externallinks',
        'datemodif',
        'datecreation',
        'date',
        'secure',
        'authors',
        'visitcount',
        'editcount',
        'displaycount',
        'version',
    ];

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data = [])
    {
        $this->pagevarlist = Page::getclassvars();
        $this->hydrate($data);
    }




    /**
     * Reset filters and sort properties to default value
     */
    public function resetall(): void
    {
        $varlist = get_class_vars(self::class);

        foreach ($varlist as $var => $default) {
            if (in_array($var, self::DATALIST)) {
                $method = 'set' . $var;
                $this->$method($default);
            }
        }
    }

    /**
     * @return bool indicating if any filters are actives
     */
    public function isfiltered(): bool
    {
        $defaultvarlist = get_class_vars(self::class);
        foreach (self::FILTERLIST as $var) {
            if ($this->$var !== $defaultvarlist[$var]) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $var                   Opt property
     * @return bool                         true if propery is set to default value, otherwhise : false
     *
     * @throws InvalidArgumentException if $var isn't a Opt property
     */
    public function isdefault(string $var): bool
    {
        $defaultvarlist = get_class_vars(self::class);
        if (!key_exists($var, $defaultvarlist)) {
            throw new InvalidArgumentException("$var is not an " . get_class($this) . " Class property");
        }
        return ($defaultvarlist[$var] === $this->$var);
    }

    /**
     * Reset specific default for specified object property
     *
     * @param string $var
     */
    public function reset(string $var): void
    {
        $varlist = get_class_vars(self::class);
        if (in_array($var, $varlist)) {
            $this->$var = $varlist[$var];
        }
    }

    /**
     * @todo Move this out of this Class
     */
    public function submit(): void
    {
        if (isset($_GET['submit'])) {
            if ($_GET['submit'] == 'reset') {
                $_SESSION['opt'] = [];
            } elseif ($_GET['submit'] == 'filter') {
                $this->getall();
            }
        } else {
            $this->sessionall();
        }
    }

    /**
     * @todo Move this out of this Class
     */
    public function getall(): void
    {
        foreach (self::DATALIST as $method) {
            if (method_exists($this, $method)) {
                if (isset($_GET[$method])) {
                    $setmethod = 'set' . $method;
                    $this->$setmethod($_GET[$method]);
                } else {
                    $this->reset($method);
                }
                $_SESSION['opt'][$method] = $this->$method();
            }
        }
    }

    /**
     * @todo Move this out of this Class
     */
    public function sessionall(): void
    {
        if (isset($_SESSION['opt'])) {
            $this->hydrate($_SESSION['opt']);
        }
    }

    /**
     * @return string http encoded query
     */
    public function getaddress(): string
    {
        $object = $this->paramdiff();
        $object['submit'] = 'filter';

        return '?' . urldecode(http_build_query($object));
    }

    /**
     * Used to generate links in home table header
     */
    public function sortbyorder(string $sortby = ''): string
    {
        $object = $this->drylist(self::DATALIST, true, self::HTML_DATETIME_LOCAL);
        if (!empty($sortby)) {
            $object['sortby'] = $sortby;
        }
        $object['order'] = $object['order'] * -1;
        $object['submit'] = 'filter';

        return '?' . urldecode(http_build_query($object));
    }

    /**
     * Get the link list for each tags of a page
     *
     * @param string[] $taglist List of tag to be analysed
     * @return string html code to be printed
     */
    public function taglinks(array $taglist = []): string
    {
        $tagstring = "";
        foreach ($taglist as $tag) {
            $href = $this->getfilteraddress(['tagfilter' => [$tag]]);
            $tagstring .= "<a class=\"tag tag_$tag\" href=\"?$href\" >$tag</a>\n";
        }
        return $tagstring;
    }

    /**
     * Get the link list for each authors of an page
     *
     * @param string[] $authorlist List of author to be
     * @return string html code to be printed
     */
    public function authorlinks(array $authorlist = []): string
    {
        $authorstring = "";
        foreach ($authorlist as $author) {
            $href = $this->getfilteraddress(['authorfilter' => [$author]]);
            $authorstring .= "<a class=\"author author_$author\" href=\"?$href\" >$author</a>\n";
        }
        return $authorstring;
    }

    public function securelink(int $level, string $secure): string
    {
        $href = $this->getfilteraddress(['secure' => $level]);
        return "<a class=\"secure $secure\" href=\"?$href\">$secure</a>\n";
    }

    /**
     * @param string[] $linktolist
     */
    public function linktolink(array $linktolist): string
    {
        $linktostring = "";
        foreach ($linktolist as $linkto) {
            $href = $this->getfilteraddress(['linkto' => $linkto]);
            $linktostring .= "<a class=\"linkto\" href=\"?$href\" >$linkto</a>\n";
        }
        return $linktostring;
    }

    /**
     * @param array<string, mixed> $vars
     */
    public function getfilteraddress(array $vars = []): string
    {
        $object = $this->drylist(self::DATALIST, true, self::HTML_DATETIME_LOCAL);
        $object = array_merge($object, $vars);
        $object['submit'] = 'filter';
        return urldecode(http_build_query($object));
    }

    /**
     * Get the query as http string
     *
     * @return string                       Parameters as a HTTP string. If not empty, start with a `?`
     */
    public function getquery(): string
    {
        $query = $this->paramdiff();
        $httpquery = urldecode(http_build_query($query));
        if (!empty($httpquery)) {
            $httpquery = "?$httpquery";
        }
        return $httpquery;
    }

    /**
     * @return array<string, mixed>         associative array of object's filtering and sorting values
     *                                      that are not default ones.
     */
    protected function paramdiff(): array
    {
        /** @var array<string, mixed> */
        $class = get_class_vars(get_class($this));
        $object = $this->dry();
        $class['pageidlist'] = $object['pageidlist'];
        $class['pagevarlist'] = $object['pagevarlist'];
        $class['taglist'] = $object['taglist'];
        $class['authorlist'] = $object['authorlist'];
        return array_diff_assoc_recursive($object, $class);
    }


    // _______________________________________________ G E T _______________________________________________

    public function sortby(): string
    {
        return $this->sortby;
    }

    public function order(): int
    {
        return $this->order;
    }

    public function secure(): int
    {
        return $this->secure;
    }

    /**
     * @return string[]
     */
    public function tagfilter(): array
    {
        return $this->tagfilter;
    }

    public function tagcompare(): string
    {
        return $this->tagcompare;
    }

    public function tagnot(): bool
    {
        return $this->tagnot;
    }

    /**
     * @return string[]
     */
    public function authorfilter(): array
    {
        return $this->authorfilter;
    }

    public function authorcompare(): string
    {
        return $this->authorcompare;
    }

    public function linkto(): string
    {
        return $this->linkto;
    }

    /**
     * @return array<string, int>           Array where keys are tags and value the number of pages
     */
    public function taglist(): array
    {
        return $this->taglist;
    }

    /**
     * @return array<string, int>
     */
    public function authorlist(): array
    {
        return $this->authorlist;
    }

    /**
     * @return DateTimeImmutable|string|null
     */
    public function since(string $option = 'date')
    {
        if ($this->since === null) {
            return null;
        }
        return $this->datetransform('since', $option);
    }

    /**
     * @return DateTimeImmutable|string|null
     */
    public function until(string $option = 'date')
    {
        if ($this->until === null) {
            return null;
        }
        return $this->datetransform('until', $option);
    }

    /**
     * @return bool                         True if filter only show page with geo datas
     */
    public function geo(): bool
    {
        return $this->geo;
    }

    public function version(): int
    {
        return $this->version;
    }

    public function invert(): bool
    {
        return $this->invert;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    /**
     * @return string[]
     */
    public function pagevarlist(): array
    {
        return $this->pagevarlist;
    }

    /**
     * @return string[]
     */
    public function pageidlist(): array
    {
        return $this->pageidlist;
    }


    // __________________________________________________ S E T _____________________________________________

    public function setsortby(string $sortby): void
    {
        if (in_array($sortby, $this->pagevarlist) && in_array($sortby, self::SORTBYLIST)) {
            $this->sortby = strtolower(strip_tags($sortby));
        }
    }

    /**
     * @param int $order
     */
    public function setorder(int $order): void
    {
        if (in_array($order, [-1, 1])) {
            $this->order = $order;
        }
    }

    /**
     * @param string[] $tagfilter
     */
    public function settagfilter(array $tagfilter): void
    {
        if (is_array($tagfilter)) {
            $this->tagfilter = $tagfilter;
        }
    }

    public function settagcompare(string $tagcompare): void
    {
        if (in_array($tagcompare, self::COMPARE)) {
            $this->tagcompare = $tagcompare;
        }
    }

    public function settagnot(bool $tagnot): void
    {
        $this->tagnot = $tagnot;
    }

    /**
     * @param string[] $authorfilter
     */
    public function setauthorfilter(array $authorfilter): void
    {
        $this->authorfilter = $authorfilter;
    }

    public function setauthorcompare(string $authorcompare): void
    {
        if (in_array($authorcompare, self::COMPARE)) {
            $this->authorcompare = $authorcompare;
        }
    }

    /**
     * @param int<0, 5> $secure
     */
    public function setsecure(int $secure): void
    {
        if ($secure >= 0 && $secure <= 5) {
            $this->secure = $secure;
        }
    }

    public function setlinkto(string $linkto): bool
    {
        if (empty($this->pageidlist)) {
            $this->linkto = Model::idclean($linkto);
            return true;
        } elseif (in_array($linkto, $this->pageidlist)) {
            $this->linkto = Model::idclean($linkto);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param Page[] $pagelist
     */
    public function settaglist(array $pagelist): void
    {
        $taglist = [];
        foreach ($pagelist as $page) {
            foreach ($page->tag('array') as $tag) {
                if (!array_key_exists($tag, $taglist)) {
                    $taglist[$tag] = 1;
                } else {
                    $taglist[$tag]++;
                }
            }
        }
        arsort($taglist);
        $this->taglist = $taglist;
    }

    /**
     * @param Page[] $pagelist
     */
    public function setauthorlist(array $pagelist): void
    {
        $authorlist = [];
        foreach ($pagelist as $page) {
            foreach ($page->authors('array') as $author) {
                if (!array_key_exists($author, $authorlist)) {
                    $authorlist[$author] = 1;
                } else {
                    $authorlist[$author]++;
                }
            }
        }
        arsort($authorlist);
        $this->authorlist = $authorlist;
    }

    /**
     * @param DateTimeImmutable|string $since
     */
    public function setsince($since): void
    {
        if ($since instanceof DateTimeImmutable) {
            $this->since = $since;
        } elseif (is_string($since)) {
            if (empty($since)) {
                $this->since = null;
            } else {
                $this->since = DateTimeImmutable::createFromFormat(
                    self::HTML_DATETIME_LOCAL,
                    $since,
                    new DateTimeZone('Europe/Paris')
                );
            }
        } else {
            $this->since = null;
        }
    }

    /**
     * @param DateTimeImmutable|string $until
     */
    public function setuntil($until): void
    {
        if ($until instanceof DateTimeImmutable) {
            $this->until = $until;
        } elseif (is_string($until)) {
            if (empty($until)) {
                $this->until = null;
            } else {
                $this->until = DateTimeImmutable::createFromFormat(
                    self::HTML_DATETIME_LOCAL,
                    $until,
                    new DateTimeZone('Europe/Paris')
                );
            }
        } else {
            $this->until = null;
        }
    }

    public function setgeo(bool $geo): void
    {
        $this->geo = $geo;
    }

    public function setversion(int $version): void
    {
        if ($version === 0 || key_exists($version, Page::VERSIONS)) {
            $this->version = $version;
        }
    }

    public function setinvert(bool $invert): void
    {
        $this->invert = $invert;
    }

    public function setlimit(int $limit): void
    {
        if ($limit < 0) {
            $limit = 0;
        } elseif ($limit >= 10000) {
            $limit = 9999;
        }
        $this->limit = $limit;
    }

    /**
     * Import list of pages IDs
     *
     * @param array<string|Page> $pageidlist could be array of IDs or Page Object
     *
     * @return bool false if array content isn't string or Pages, otherwise : true
     */
    public function setpageidlist(array $pageidlist): bool
    {
        $idlist = [];
        foreach ($pageidlist as $item) {
            if (is_string($item)) {
                $idlist[] = $item;
            } elseif ($item instanceof Page) {
                $idlist[] = $item->id();
            } else {
                return false;
            }
        }
        $this->pageidlist = $idlist;
        return true;
    }
}
