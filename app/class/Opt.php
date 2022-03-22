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

    protected array $authorfilter = [];
    protected string $authorcompare = 'AND';
    protected int $secure = 4;
    protected string $linkto = '';
    protected array $taglist = [];
    protected array $authorlist = [];
    protected bool $invert = false;
    protected int $limit = 0;
    /** @var DateTimeImmutable $since */
    protected ?DateTimeImmutable $since = null;
    /** @var DateTimeImmutable $until */
    protected ?DateTimeImmutable $until = null;

    protected $pageidlist = [];

    /** @var array $pagevarlist List fo every properties of an Page object */
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
        'invert',
        'limit'
    ];

    protected const DATALIST = [...self::SORTLIST, ...self::FILTERLIST];

    public function __construct(array $data = [])
    {
        $this->hydrate($data);
        $page = new Page();
        $this->pagevarlist = ($page->getobjectvars());
    }




    /**
     * Reset all properties to default value
     */
    public function resetall(): void
    {
        $varlist = get_class_vars(self::class);

        foreach ($varlist as $var => $default) {
            $method = 'set' . $var;
            $this->$method($default);
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
     * @param string $var Opt property
     * @throws InvalidArgumentException if $var isn't a Opt property
     * @return bool true if propery is set to default value, otherwhise : false
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
     *  @param string $var
     */
    public function reset(string $var): void
    {
        $varlist = get_class_vars(self::class);
        if (in_array($var, $varlist)) {
            $this->$var = $varlist[$var];
        }
    }

    public function submit()
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

    public function getall()
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

    public function sessionall()
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
        $object = $this->drylist(self::DATALIST, self::HTML_DATETIME_LOCAL);
        $object['submit'] = 'filter';

        return '?' . urldecode(http_build_query($object));
    }

    public function sortbyorder($sortby = "")
    {
        $object = $this->drylist(self::DATALIST, self::HTML_DATETIME_LOCAL);
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
     * @param array $taglist List of tag to be abalysed
     * @return string html code to be printed
     */
    public function taglinks(array $taglist = []): string
    {
        $tagstring = "";
        foreach ($taglist as $tag) {
            $href = $this->getfilteraddress(['tagfilter' => [$tag]]);
            $tagstring .= '<a class="tag tag_' . $tag . '" href="?' . $href . '" >' . $tag . '</a>' . PHP_EOL;
        }
        return $tagstring;
    }

    /**
     * Get the link list for each authors of an page
     *
     * @param array $authorlist List of author to be
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

    public function securelink(int $level, string $secure)
    {
        $href = $this->getfilteraddress(['secure' => $level]);
        return "<a class=\"secure $secure\" href=\"?$href\">$secure</a>\n";
    }

    public function linktolink(array $linktolist)
    {
        $linktostring = "";
        foreach ($linktolist as $linkto) {
            $href = $this->getfilteraddress(['linkto' => $linkto]);
            $linktostring .= "<a class=\"linkto\" href=\"?$href\" >$linkto</a>\n";
        }
        return $linktostring;
    }


    public function getfilteraddress(array $vars = [])
    {
        // array_filter($vars, function ())
        $object = $this->drylist(self::DATALIST, self::HTML_DATETIME_LOCAL);
        $object = array_merge($object, $vars);
        $object['submit'] = 'filter';
        return urldecode(http_build_query($object));
    }



    /**
     * Get the query as http string
     *
     * @return string The resulted query
     */
    public function getquery(): string
    {
        $class = get_class_vars(get_class($this));
        $object = $this->dry(self::HTML_DATETIME_LOCAL);
        $class['pagevarlist'] = $object['pagevarlist'];
        $class['taglist'] = $object['taglist'];
        $class['authorlist'] = $object['authorlist'];
        $query = array_diff_assoc_recursive($object, $class);

        return urldecode(http_build_query($query));
    }

    public function parsetagcss(string $cssstring)
    {
        $classprefix = 'tag';
        $pattern = '%a\.' . $classprefix . '\_([a-z0-9\-\_]*)\s*\{\s*(background-color):\s*(#[A-F0-6]{6})\;\s*\}%';
        preg_match($pattern, $cssstring, $matches);
        foreach ($matches as $value) {
        }
    }

    public function tocss($cssdatas)
    {
        $string = '';
        foreach ($cssdatas as $element => $css) {
            $string .= PHP_EOL . $element . ' {';
            foreach ($css as $param => $value) {
                $string .= PHP_EOL . '    ' . $param . ': ' . $value . ';';
            }
            $string .= PHP_EOL . '}' . PHP_EOL;
        }
        return $string;
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

    public function tagfilter(): array
    {
        return $this->tagfilter;
    }

    public function tagcompare(): string
    {
        return $this->tagcompare;
    }

    public function tagnot()
    {
        return $this->tagnot;
    }

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

    public function taglist(): array
    {
        return $this->taglist;
    }

    public function authorlist(): array
    {
        return $this->authorlist;
    }

    /**
     * @return DateTimeImmutable|string|null
     */
    public function since($option = "date")
    {
        return $this->datetransform('since', $option);
    }

    /**
     * @return DateTimeImmutable|string|null
     */
    public function until($option = "date")
    {
        return $this->datetransform('until', $option);
    }

    public function invert(): bool
    {
        return $this->invert;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function pagevarlist(): array
    {
        return $this->pagevarlist;
    }

    public function pageidlist(): array
    {
        return $this->pageidlist;
    }


    // __________________________________________________ S E T _____________________________________________

    public function setsortby($sortby)
    {
        if (is_string($sortby) && in_array($sortby, $this->pagevarlist) && in_array($sortby, Model::COLUMNS)) {
            $this->sortby = strtolower(strip_tags($sortby));
        }
    }

    public function setorder($order)
    {
        $order = intval($order);
        if (in_array($order, [-1, 0, 1])) {
            $this->order = $order;
        }
    }

    public function settagfilter($tagfilter)
    {
        if (!empty($tagfilter) && is_array($tagfilter)) {
            $this->tagfilter = $tagfilter;
        }
    }

    public function settagcompare($tagcompare)
    {
        if (in_array($tagcompare, self::COMPARE)) {
            $this->tagcompare = $tagcompare;
        }
    }

    public function settagnot($tagnot)
    {
        $this->tagnot = boolval($tagnot);
    }

    public function setauthorfilter($authorfilter)
    {
        if (!empty($authorfilter) && is_array($authorfilter)) {
            $this->authorfilter = $authorfilter;
        }
    }

    public function setauthorcompare($authorcompare)
    {
        if (in_array($authorcompare, self::COMPARE)) {
            $this->authorcompare = $authorcompare;
        }
    }

    public function setsecure($secure)
    {
        if ($secure >= 0 && $secure <= 5) {
            $this->secure = intval($secure);
        }
    }

    public function setlinkto($linkto): bool
    {
        if (is_string($linkto)) {
            if (empty($this->pageidlist)) {
                $this->linkto = Model::idclean($linkto);
                return true;
            } elseif (in_array($linkto, $this->pageidlist)) {
                $this->linkto = Model::idclean($linkto);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function settaglist(array $pagelist)
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
        $taglistsorted = arsort($taglist);
        $this->taglist = $taglist;
    }

    public function setauthorlist(array $pagelist)
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
        $authorlistsorted = arsort($authorlist);
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
        }
    }

    public function setinvert($invert)
    {
        $this->invert = boolval($invert);
    }

    public function setlimit($limit)
    {
        $limit = intval($limit);
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
     * @param array $pageidlist could be array of IDs or array of Page Object
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
