<?php

namespace Wcms;

use AltoRouter;
use DOMDocument;
use DOMElement;
use DOMNodeList;
use Exception;
use InvalidArgumentException;
use LogicException;
use Michelf\MarkdownExtra;
use RuntimeException;
use VStelmakh\UrlHighlight\Highlighter\HtmlHighlighter;
use VStelmakh\UrlHighlight\UrlHighlight;
use VStelmakh\UrlHighlight\Validator\Validator;

abstract class Servicerender
{
    /** @var AltoRouter */
    protected ?AltoRouter $router;

    /** @var Modelpage */
    protected Modelpage $pagemanager;

    protected Page $page;

    /** @var string[] */
    protected $linkto = [];

    protected $sum = [];

    /** @var bool If true, internal links target a new tab */
    protected bool $internallinkblank;

    /** @var bool If true, external links target a new tab */
    protected bool $externallinkblank;

    /** @var bool True if the page need post process */
    protected bool $postprocessaction = false;

    /**
     * @var Bookmark[]                      Associative array of Bookmarks using fullmatch as key
     * */
    protected $rsslist = [];

    /** @var bool Indicate presence of an included MAP, meant to call the script in page's Head */
    protected bool $map = false;

    /**
     * @param AltoRouter $router            Router used to generate urls
     * @param Modelpage $pagemanager        [optionnal] can be usefull if a pagemanager already store a page list
     */
    public function __construct(
        AltoRouter $router,
        Modelpage $pagemanager,
        bool $externallinkblank = false,
        bool $internallinkblank = false
    ) {
        $this->router = $router;
        $this->pagemanager = $pagemanager;
        $this->externallinkblank = $externallinkblank;
        $this->internallinkblank = $internallinkblank;
    }


    /**
     * Render a full page as HTML
     *
     * @param Page $page                    page to render
     *
     * @return string                       HTML render of the page
     */
    public function render(Page $page): string
    {
        $this->page = $page;

        return $this->gethmtl();
    }

    /**
     * Render page's primary content to be used in RSS feed
     *
     * @param Page $page                    Page to render
     *
     * @return string                       HTML Parsed primary content of a page
     *
     * @todo                                render absolute media links
     */
    abstract public function renderprimary(Page $page): string;

    /**
     * Used to convert the markdown user manual to html document
     *
     * @param string $text Input text in markdown
     * @return string html formated text
     */
    public function rendermanual(string $text): string
    {
        $text = $this->markdown($text);
        $text = $this->headerid($text, 1, 5, 0);
        return $text;
    }


    /**
     * Combine body and head to create html file
     *
     * @return string html string
     */
    protected function gethmtl()
    {

        $body = $this->bodyconstructor($this->readbody());
        $parsebody = $this->bodyparser($body);
        $this->postprocessaction = $this->checkpostprocessaction($parsebody);
        $head = $this->gethead();

        $lang = !empty($this->page->lang()) ? $this->page->lang() : Config::lang();
        $langproperty = 'lang="' . $lang . '"';
        $html = "<!DOCTYPE html>\n<html $langproperty >\n<head>\n$head\n</head>";
        $html .= "\n$parsebody\n</html>\n";

        return $html;
    }


    protected function readbody()
    {
        if (!empty($this->page->templatebody())) {
            $templateid = $this->page->templatebody();
            try {
                $body = $this->pagemanager->get($templateid)->body();
            } catch (RuntimeException $e) {
                Logger::errorex($e);
                $body = $this->page->body();
                $this->page->settemplatebody('');
            }
        } else {
            $body = $this->page->body();
        }
        return $body;
    }


    /**
     * Analyse BODY, include basic inclusions
     *
     * @param string $body as the string BODY of the page
     *
     * @return string as the full rendered BODY of the page
     */
    protected function bodyconstructor(string $body): string
    {
        $body = $this->winclusions($body);
        return $body;
    }

    /**
     * Return HEAD html element of a page
     */
    protected function gethead(): string
    {
        $id = $this->page->id();
        $globalpath = Model::dirtopath(Model::GLOBAL_CSS_FILE);
        $fontcsspath = Model::dirtopath(Model::FONTS_CSS_FILE);
        $renderpath = Model::renderpath();
        $description = $this->page->description();
        $title = $this->page->title();
        $suffix = Config::suffix();
        $url = Config::url();

        $head = '';

        // redirection
        if (!empty($this->page->redirection())) {
            try {
                if (Model::idcheck($this->page->redirection())) {
                    $url = $this->upage($this->page->redirection());
                } else {
                    $url = getfirsturl($this->page->redirection());
                }
                $head .= "\n<meta http-equiv=\"refresh\" content=\"{$this->page->refresh()}; URL=$url\" />";
            } catch (\Exception $e) {
                // TODO : send render error
            }
        }

        $head .= "<meta charset=\"utf-8\" />\n";
        $head .= "<title>$title$suffix</title>\n";

        $favicon = Model::faviconpath() . $this->pagemanager->getpagefavicon($this->page);
        $head .= "<link rel=\"shortcut icon\" href=\"$favicon\" type=\"image/x-icon\">";

        $head .= "<meta name=\"description\" content=\"$description\" />\n";
        $head .= "<meta name=\"viewport\" content=\"width=device-width\" />\n";
        $head .= "<meta name=\"generator\" content=\"W-cms\" />\n";

        $head .= "<meta property=\"og:type\" content=\"website\" />";
        $head .= "<meta property=\"og:title\" content=\"$title$suffix\">\n";
        $head .= "<meta property=\"og:description\" content=\"$description\">\n";

        $thumbnail = Config::domain() . Model::thumbnailpath() . $this->pagemanager->getpagethumbnail($this->page);
        $head .= "<meta property=\"og:image\" content=\"$thumbnail\">\n";

        $head .= "<meta property=\"og:url\" content=\"$url$id\">\n";

        foreach ($this->rsslist as $bookmark) {
            $atompath = Servicerss::atompath($bookmark->id());
            $title = $bookmark->name();
            $head .= "<link href=\"$atompath\" type=\"application/atom+xml\" rel=\"alternate\" title=\"$title\" />";
        }

        $head .= "\n" . $this->page->customhead() . "\n";

        foreach ($this->page->externalcss() as $externalcss) {
            $head .= "<link href=\"$externalcss\" rel=\"stylesheet\" />\n";
        }

        if (file_exists(Model::GLOBAL_CSS_FILE)) {
            $head .= "<link href=\"{$globalpath}\" rel=\"stylesheet\" />\n";
        }
        if (file_exists(Model::FONTS_CSS_FILE)) {
            $head .= "<link href=\"{$fontcsspath}\" rel=\"stylesheet\" />\n";
        }

        $head .= $this->recursivecss($this->page);
        $head .= "<link href=\"$renderpath$id.css\" rel=\"stylesheet\" />\n";

        try {
            foreach ($this->pagemanager->getpagejavascripttemplates($this->page) as $template) {
                $templateid = $template->id();
                $head .= "<script src=\"$renderpath$templateid.js\" async/></script>\n";
            }
        } catch (RuntimeException $e) {
            Logger::warningex($e);
        }
        if (!empty($this->page->javascript())) {
            $head .= "<script src=\"$renderpath$id.js\" async/></script>\n";
        }
        if ($this->map) {
            $mapcss = Model::jspath() . 'pagemap.bundle.css';
            $mapjs = Model::jspath() . 'pagemap.bundle.js';
            $head .= "<link href=\"$mapcss\" rel=\"stylesheet\" />\n"
                . "<script type=\"module\" src=\"$mapjs\"></script>\n";
        }
        return $head;
    }

    /**
     * This create a HTML link for every stylsheet that are templated
     *
     * @param Page $page                    Page being rendered
     * @return string                       HTML to insert into <head> of page
     */
    protected function recursivecss(Page $page): string
    {
        $head = "";
        try {
            $templates = $this->pagemanager->getpagecsstemplates($page);
            foreach ($templates as $template) {
                foreach ($template->externalcss() as $externalcss) {
                    $head .= "<link href=\"$externalcss\" rel=\"stylesheet\" />\n";
                }
                $head .= '<link href="' . Model::renderpath() . $template->id() . '.css" rel="stylesheet" />';
                $head .= "\n";
            }
        } catch (RuntimeException $e) {
            Logger::errorex($e);
        }
        return $head;
    }


    /**
     * Perfom W syntax inclusions
     */
    protected function winclusions($text)
    {
        $text = $this->date($text);
        $text = $this->thumbnail($text);
        $text = $this->pageid($text);
        $text = $this->url($text);
        $text = $this->path($text);
        $text = $this->title($text);
        $text = $this->description($text);
        $text = $this->pageoptlist($text);
        $text = $this->automedialist($text);
        $text = $this->pageoptmap($text);
        $text = $this->randomopt($text);
        $text = $this->authors($text);
        return $text;
    }

    protected function bodyparser(string $html)
    {
        $html = $this->summary($html);

        $html = $this->rss($html);
        $html = $this->wikiurl($html);

        $html = "<body>\n$html\n</body>";
        $html = $this->htmlparser($html);


        return $html;
    }

    /**
     * Replace `%TITLE%` code with page's title
     */
    protected function title($text)
    {
        return str_replace('%TITLE%', $this->page->title(), $text);
    }


    /**
     * Replace `%DESCRIPTION%` code with page's description
     */
    protected function description($text)
    {
        return str_replace('%DESCRIPTION%', $this->page->description(), $text);
    }

    /**
     * Look for datas about pages.
     *
     * @param string $text the page text as html
     */
    protected function richlink(string $text): string
    {
        $text = preg_replace('#<a(.*href="(https?:\/\/(.+))".*)>\2</a>#', "<a$1>$3</a>", $text);
        return $text;
    }

    /**
     * Replace plain URL with HTML link pointing to their address.
     *
     * This will also include `target=_blank` and `class=external` attributes.
     */
    protected function autourl($text): string
    {
        $options = ["class" => "external"];
        if ($this->externallinkblank) {
            $options['target'] = '_blank';
        }
        $validator = new Validator(false);
        $highlighter = new HtmlHighlighter("http", $options);
        $urlHighlight = new UrlHighlight($validator, $highlighter);
        $text = $urlHighlight->highlightUrls($text);
        return $text;
    }

    /**
     * Add `external` or `internal` class attribute in `<a>` anchor HTML link tags.
     * In case of internal link, add `media` or `page` class depending of the kind of ressource it is pointing to.     *
     * For internal link to pages, indicate if page exist or not.
     * If the link point to the current page, add `current_page` class.
     * If it exist, add description in title and privacy as class.
     *
     * Add `external` or `internal` class attribute in `<img>`, `audio`, `video` or `source` HTML tags.
     *
     * Keep existing class and remove duplicates or useless spaces in class attribute
     */
    protected function htmlparser(string $html): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        /** Force UTF-8 encoding for loadHTML by defining it in the content itself with an XML tag that need to be removed later */
        $html = '<?xml encoding="utf-8" ?>' . $html;
        /** @phpstan-ignore-next-line Error supposed to be thrown here but is'nt */
        $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
        $dom->removeChild($dom->firstChild);
        $links = $dom->getElementsByTagName('a');
        foreach ($links as $link) {
            assert($link instanceof DOMElement);
            $class = $link->getAttribute('class');
            $classes = explode(' ', $class);
            $classes = array_filter($classes, function (string $var) {
                return !empty($var);
            });
            $href = $link->getAttribute('href');
            if (preg_match('~^https?:\/\/~', $href)) {
                $classes[] = 'external';
                if (!$link->hasAttribute('target') && $this->externallinkblank) {
                    $link->setAttribute('target', '_blank');
                }
            } elseif (preg_match('~^([a-z0-9-_]+)((\/?#[a-z0-9-_]+)|(\/([\w\-\%\[\]\=\?\&]*)))?$~', $href, $out)) {
                $classes[] = 'internal';
                $classes[] = 'page';
                $fragment = $out[2] ?? '';
                $link->setAttribute('href', $this->upage($out[1]) . $fragment);
                if (isset($out[5]) && in_array($out[5], ['add', 'edit', 'update', 'render', 'download', 'delete'])) {
                    $classes[] = $out[5];
                }
                try {
                    $page = $this->pagemanager->get($out[1]);
                    if (!$link->hasAttribute('title')) {
                        $link->setAttribute('title', $page->description());
                    }
                    $classes[] = 'exist';
                    if ($this->page->id() === $page->id()) {
                        $classes[] = 'current_page';
                    }
                    $classes[] = $page->secure('string');
                    $this->linkto[] = $page->id();
                } catch (RuntimeException $e) { // Page does not exist
                    $link->setAttribute('title', Config::existnot());
                    $classes[] = 'existnot';
                    // TODO: store internal link that exist not in $this
                }
                if (!$link->hasAttribute('target') && $this->internallinkblank) {
                    $link->setAttribute('target', '_blank');
                }
            // Links pointing to medias
            } elseif (preg_match('~^(?!([\/#]|[a-zA-Z\.\-\+]+:|\.+\/))([^"]+\.[^";]+)$~', $href, $out)) {
                $classes[] = 'internal';
                $classes[] = 'media';
                $link->setAttribute('href', Model::mediapath() . $out[2]);
            } elseif (preg_match('~^\.\/media~', $href)) {
                $classes[] = 'internal';
                $classes[] = 'media';
            }
            if (!empty($classes)) {
                $link->setAttribute('class', implode(' ', array_unique($classes)));
            }
        }
        $images = $dom->getElementsByTagName('img');
        $this->sourceparser($images);
        $sources = $dom->getElementsByTagName('source');
        $this->sourceparser($sources);
        $audios = $dom->getElementsByTagName('audio');
        $this->sourceparser($audios);
        $videos = $dom->getElementsByTagName('video');
        $this->sourceparser($videos);
        // By passing the documentElement to saveHTML, special chars are not converted to entities
        return $dom->saveHTML($dom->documentElement);
    }

    /**
     * Edit `src` attributes in media HTML tags
     */
    protected function sourceparser(DOMNodeList $sourcables): void
    {
        foreach ($sourcables as $sourcable) {
            assert($sourcable instanceof DOMElement);
            $src = $sourcable->getAttribute('src');
            $class = $sourcable->getAttribute('class');
            $classes = explode(' ', $class);
            $classes = array_filter($classes, function (string $var) {
                return !empty($var);
            });
            if (preg_match('~^https?:\/\/~', $src)) {
                $classes[] = 'external';
            } elseif (preg_match('~^(?!([\/#]|[a-zA-Z\.\-\+]+:|\.+\/))([^"]+\.[^";]+)$~', $src, $out)) {
                $sourcable->setAttribute('src', Model::mediapath() . $out[2]);
                $classes[] = 'internal';
            } elseif (preg_match('~^\.\/media~', $src)) {
                $classes[] = 'internal';
            }
            if (!empty($classes)) {
                $sourcable->setAttribute('class', implode(' ', array_unique($classes)));
            }
            if ($sourcable->tagName === 'img' && Config::lazyloadimg()) {
                $sourcable->setAttribute('loading', 'lazy');
            }
        }
    }

    /**
     * Replace wiki links [[page_id]] with HTML link
     */
    protected function wikiurl(string $text): string
    {
        $rend = $this;
        $text = preg_replace_callback(
            '%\[\[([a-z0-9_-]+)\/?(#[\w-]+)?\]\]%',
            function ($matches) use ($rend) {
                try {
                    $matchpage = $rend->pagemanager->get($matches[1]);
                    $fragment = $matches[2] ?? '';
                    $href = $matches[1] . $fragment;
                    $a = $matchpage->title();
                } catch (RuntimeException $e) {
                    $href = $matches[1];
                    $a = $matches[1];
                }
                return '<a href="' . $href . '">' . $a . '</a>';
            },
            $text
        );
        return $text;
    }

    /**
     * Add Id to html header elements and store the titles in the `$this->sum` var
     *
     * @param string $text Input html document to scan
     * @param int $min Maximum header deepness to look for. Min = 1 Max = 6 Default = 1
     * @param int $max Maximum header deepness to look for. Min = 1 Max = 6 Default = 6
     * @param string $element Name of element being analysed. Leave empty if using Markdown field
     * @param int $anchormode Mode of anchor link display. see Element HEADERANCHORMODES
     *
     * @return string text with id in header
     */

    protected function headerid(string $text, int $min, int $max, int $anchormode, string $element = ''): string
    {
        if (empty($element)) {
            $element = md5($text);
        }
        if ($min > 6 || $min < 1) {
            $min = 6;
        }
        if ($max > 6 || $max < 1) {
            $max = 6;
        }

        $text = preg_replace_callback(
            "/<h([$min-$max])((.*)id=\"([^\"]*)\"(.*)|.*)>(.+)<\/h[$min-$max]>/mU",
            function ($matches) use ($element, $anchormode) {
                $level = $matches[1];
                $beforeid = $matches[3];
                $id = $matches[4];
                $afterid = $matches[5];
                $content = $matches[6];
                // if no custom id is defined, use idclean of the content as id
                if (empty($id)) {
                    $id = Model::idclean($content);
                }
                $this->sum[$element][] = new Header($id, intval($level), $content);
                switch ($anchormode) {
                    case Element::HEADERANCHORLINK:
                        $content = "<a href=\"#$id\">$content</a>";
                        break;

                    case Element::HEADERANCHORHASH:
                        $content .= "<span class=\"nbsp\">&nbsp;</span><a class=\"headerlink\" href=\"#$id\">#</a>";
                        break;
                }
                return "<h$level $beforeid id=\"$id\" $afterid>$content</h$level>";
            },
            $text
        );
        return $text;
    }

    protected function markdown($text)
    {
        $fortin = new MarkdownExtra();
        // id in headers
        // $fortin->header_id_func = function ($header) {
        //  return preg_replace('/[^\w]/', '', strtolower($header));
        // };
        $fortin->hard_wrap = Config::markdownhardwrap();
        $text = $fortin->transform($text);
        return $text;
    }

    /**
     * Match `%INCLUDE?params=values&...%`
     *
     * @param string $text                  Input text to scan
     * @param string $include               Word to match `%$include%`
     *
     * @return array Ordered array containing an array of `fullmatch`, `type` and `options`
     */
    protected function match(string $text, string $include): array
    {
        preg_match_all('~\%(' . $include . ')(\?([a-zA-Z0-9:\[\]\&=\-_\/\%\+\*\;]*))?\%~', $text, $out);

        $matches = [];

        foreach ($out[0] as $key => $match) {
            $matches[$key] = ['fullmatch' => $match, 'type' => $out[1][$key], 'options' => $out[3][$key]];
        }
        return $matches;
    }

    /**
     * Check for media list call in the text and insert media list
     * @param string $text Text to scan and replace
     *
     * @return string Output text
     */
    protected function automedialist(string $text): string
    {
        $matches = $this->match($text, 'MEDIA');

        if (!empty($matches)) {
            foreach ($matches as $match) {
                $medialist = new Mediaoptlist($match);
                $medialist->readoptions();
                try {
                    $text = str_replace($medialist->fullmatch(), $medialist->generatecontent(), $text);
                } catch (RuntimeException $e) {
                    Logger::errorex($e);
                }
            }
        }
        return $text;
    }

    /**
     * Check for Summary calls in the text and insert html summary
     * @param string $text Text to scan and replace
     *
     * @return string Output text
     */
    protected function summary(string $text): string
    {
        $matches = $this->match($text, 'SUMMARY');


        if (!empty($matches)) {
            foreach ($matches as $match) {
                $data = array_merge($match, ['sum' => $this->sum]);
                $summary = new Summary($data);
                $text = str_replace($summary->fullmatch(), $summary->sumparser(), $text);
            }
        }
        return $text;
    }


    /**
     * Render pages list
     */
    protected function pageoptlist(string $text): string
    {
        $matches = $this->match($text, 'LIST');
        foreach ($matches as $match) {
            try {
                $optlist = new Optlist();
                $optlist->parsehydrate($match['options'], $this->page);

                if (!empty($optlist->bookmark())) {
                    $bookmarkmanager = new Modelbookmark();
                    $bookmark = $bookmarkmanager->get($optlist->bookmark());
                    $optlist->resetall();
                    $optlist->parsehydrate($bookmark->query(), $this->page);
                    $optlist->parsehydrate($match['options'], $this->page); // Erase bookmark options with LIST ones
                }

                $pagetable = $this->pagemanager->pagetable($this->pagemanager->pagelist(), $optlist);
                $content = $optlist->listhtml($pagetable, $this, $this->page);
                $text = str_replace($match['fullmatch'], $content, $text);
            } catch (RuntimeException $e) {
                Logger::errorex($e);
            }
        }

        return $text;
    }

    /**
     * Render page maps
     */
    protected function pageoptmap(string $text): string
    {
        $matches = $this->match($text, 'MAP');
        foreach ($matches as $match) {
            try {
                $optmap = new Optmap();
                $optmap->parsehydrate($match['options'], $this->page);

                if (!empty($optmap->bookmark())) {
                    $bookmarkmanager = new Modelbookmark();
                    $bookmark = $bookmarkmanager->get($optmap->bookmark());
                    $optmap->resetall();
                    $optmap->parsehydrate($bookmark->query(), $this->page);
                    $optmap->parsehydrate($match['options'], $this->page); // Erase bookmark options with LIST ones
                }

                $pagetable = $this->pagemanager->pagetable($this->pagemanager->pagelist(), $optmap);
                $geopt = new Opt(['geo' => true]);
                $pagetable = $this->pagemanager->pagetable($pagetable, $geopt);
                $this->linkto = array_merge($this->linkto, array_keys($pagetable));
                $content = $optmap->maphtml($pagetable, $this->router);
                $text = str_replace($match['fullmatch'], $content, $text);
                $this->map = true;
            } catch (RuntimeException $e) {
                Logger::errorex($e);
            }
        }
        return $text;
    }

    /**
     * Render Random links
     */
    protected function randomopt(string $text): string
    {
        $matches = $this->match($text, 'RANDOM');
        foreach ($matches as $match) {
            $optrandom = new Optrandom();
            $optrandom->parsehydrate($match['options'], $this->page);

            if (!empty($optrandom->bookmark())) {
                try {
                    $bookmarkmanager = new Modelbookmark();
                    $bookmark = $bookmarkmanager->get($optrandom->bookmark());
                    $optrandom->resetall();
                    $optrandom->parsehydrate($bookmark->query(), $this->page);
                    $optrandom->parsehydrate($match['options'], $this->page); // Erase bookmark options with LIST ones
                } catch (RuntimeException $e) {
                    Logger::errorex($e); // bookmark does not exist
                }
            }

            $randompages = $this->pagemanager->pagetable($this->pagemanager->pagelist(), $optrandom);
            $this->linkto = array_merge($this->linkto, array_keys($randompages));
            $optrandom->setorigin($this->page->id());
            $content = $this->generate('randomdirect', [], $optrandom->getquery());
            $text = str_replace($match['fullmatch'], $content, $text);
        }
        return $text;
    }

    /**
     * Replace RSS inclusions with atom paths and store Bookmarks in `rsslist` property
     *
     * @param string $text                  Input text to analyse
     *
     * @return string                       Text with replaced valid %RSS% inclusions
     */
    protected function rss(string $text): string
    {
        $this->rsslist = $this->rssmatch($text);
        foreach ($this->rsslist as $fullmatch => $bookmark) {
            $atompath = Servicerss::atompath($bookmark->id());
            return str_replace($fullmatch, $atompath, $text);
        }
        return $text;
    }

    /**
     * Identify all RSS inclusion in text, that have a valid and bublished bookmark associated
     *
     * @param string $text                  Text to analyse
     *
     * @return Bookmark[]                   Associative array of bookmarks, using fullmatch as key
     */
    protected function rssmatch(string $text): array
    {
        $rsslist = [];
        $matches = $this->match($text, "RSS");
        foreach ($matches as $match) {
            parse_str($match['options'], $datas);
            if (isset($datas['bookmark'])) {
                $bookmarkmanager = new Modelbookmark();
                try {
                    $bookmark = $bookmarkmanager->get($datas['bookmark']);
                    if ($bookmark->ispublished()) {
                        $rsslist[$match['fullmatch']] = $bookmark;
                    }
                } catch (RuntimeException $e) {
                    // log a render error
                }
            }
        }
        return $rsslist;
    }



    protected function date(string $text): string
    {
        $dateregex = implode('|', array_keys(Clock::TYPES));
        $matches = $this->match($text, $dateregex);
        $searches = [];
        $replaces = [];
        foreach ($matches as $match) {
            $clock = new Clock(
                $match['type'],
                $this->page,
                $match['fullmatch'],
                $match['options'],
                $this->page->lang()
            );
            $searches[] = $clock->fullmatch();
            $replaces[] = $clock->format();
        }
        return str_replace($searches, $replaces, $text);
    }

    /**
     * Render thumbnail of the page
     *
     * @param string $text Text to analyse
     *
     * @return string The rendered output
     */
    protected function thumbnail(string $text): string
    {
        $src = Model::thumbnailpath() . $this->pagemanager->getpagethumbnail($this->page);
        $alt = $this->page->title();
        $img = '<img class="thumbnail" src="' . $src . '" alt="' . $alt . '">';
        $img = "\n$img\n";
        $text = str_replace('%THUMBNAIL%', $img, $text);

        return $text;
    }

    /**
     * Replace each occurence of `%PAGEID%` or `%ID%` with page ID
     * @param string $text input text
     * @return string output text with replaced elements
     */
    protected function pageid(string $text): string
    {
        return str_replace(['%PAGEID%', '%ID%'], $this->page->id(), $text);
    }

    /**
     * Replace each occurence of `%URL%` with page ID
     * @param string $text input text
     * @return string output text with replaced elements
     */
    protected function url(string $text): string
    {
        return str_replace('%URL%', Config::domain() . $this->upage($this->page->id()), $text);
    }

    /**
     * Replace each occurence of `%PATH%` with page path
     * @param string $text input text
     * @return string output text with replaced elements
     */
    protected function path(string $text): string
    {
        return str_replace('%PATH%', $this->upage($this->page->id()), $text);
    }

    /**
     * Replace `%AUTHORS%` with a rendered list of authors
     */
    protected function authors(string $text): string
    {
        $page = $this->page;
        return preg_replace_callback("~\%AUTHORS\%~", function () use ($page) {
            $usermanager = new Modeluser();
            $users = $usermanager->userlistbyid($page->authors());
            return $this->userlist($users);
        }, $text);
    }

    /**
     * Check if the page need post processing by looking for patterns
     */
    protected function checkpostprocessaction(string $text): bool
    {
        $counterpaterns = Servicepostprocess::POST_PROCESS_CODES;
        $pattern = implode('|', $counterpaterns);
        return boolval(preg_match("#($pattern)#", $text));
    }

    /**
     * Autolink Function : transform every word of more than $limit characters in internal link
     *
     * @param string $text The input text to be converted
     *
     * @return string Conversion output
     */
    protected function everylink(string $text, int $limit): string
    {
        $regex = '~([\w\-_éêèùïüîçà]{' . $limit . ',})(?![^<]*>|[^<>]*<\/)~';
        $text = preg_replace_callback($regex, function ($matches) {
            return '<a href="' . Model::idclean($matches[1]) . '">' . $matches[1] . '</a>';
        }, $text);
        return $text;
    }

    /**
     * Render an user as a <span> HTML element
     * A HTML link is added inside if user have a specified URL property
     *
     * @param User $user        User to render
     * @return string           HTML rendered <span> element
     */
    public function user(User $user): string
    {
        $name   = !empty($user->name()) ? $user->name() : $user->id();
        $id     = $user->id();
        if (!empty($user->url())) {
            $url = $user->url();
            $html = "<a href=\"$url\">$name</a>";
        } else {
            $html = $name;
        }
        $html = "<span class=\"user user-$id\" data-user-id=\"$id\">$html</span>";
        return $html;
    }

    /**
     * Render a list of Users as a HTML <ul> that may contain links to their profile pages
     *
     * @param User[] $users     List of User
     * @return string           List of user in HTML
     */
    protected function userlist(array $users): string
    {
        $html = "";
        foreach ($users as $user) {
            $html .= "<li>\n" . $this->user($user) . "\n</li>";
        }
        return "<ul class=\"userlist\">\n$html\n</ul>";
    }



    // _________________________ R O U T E S _______________________________


    /**
     * Generate page relative link for given page_id including basepath
     *
     * @param string $id                    given page ID
     * @return string                       Relative URL
     * @throws LogicException               if router fail to generate route
     */
    public function upage(string $id): string
    {
        try {
            return $this->router->generate('pageread', ['page' => $id]);
        } catch (Exception $e) {
            throw new LogicException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Generate the URL for a named route. Replace regexes with supplied parameters.
     *
     * @param string $route The name of the route.
     * @param array $params Associative array of parameters to replace placeholders with.
     * @param string $get Optionnal query GET parameters formated
     * @return string The URL of the route with named parameters in place.
     * @throws InvalidArgumentException If the route does not exist.
     *
     * @todo avoid duplication with Controller (this one a is a duplicate)
     */
    public function generate(string $route, array $params = [], string $get = ''): string
    {
        try {
            return $this->router->generate($route, $params) . $get;
        } catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }


    // _________________________ G E T ___________________________________

    public function sum()
    {
        return $this->sum;
    }

    public function linkto()
    {
        sort($this->linkto);
        $linkto = array_unique($this->linkto);
        $key = array_search($this->page->id(), $linkto);
        if (is_int($key)) {
            unset($linkto[$key]);
        }
        $this->linkto = [];
        return $linkto;
    }

    public function postprocessaction(): bool
    {
        return $this->postprocessaction;
    }
}
