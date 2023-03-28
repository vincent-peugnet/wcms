<?php

namespace Wcms;

use AltoRouter;
use DOMDocument;
use DOMElement;
use Exception;
use InvalidArgumentException;
use LogicException;
use Michelf\MarkdownExtra;
use RuntimeException;
use VStelmakh\UrlHighlight\Highlighter\HtmlHighlighter;
use VStelmakh\UrlHighlight\UrlHighlight;
use VStelmakh\UrlHighlight\Validator\Validator;

class Servicerender
{
    /** @var AltoRouter */
    protected ?AltoRouter $router;

    /** @var Modelpage */
    protected Modelpage $pagemanager;

    /**
     * @var Page                            Actual page being rendered
     * */
    protected $page;

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
     * Render a page MAIN content to be used in RSS feed
     *
     * @param Page $page                    Page to render
     *
     * @return string                       HTML Parsed MAIN content of a page
     *
     * @todo                                render absolute media links
     */
    public function rendermain(Page $page): string
    {
        $this->page = $page;
        $element = new Element($page->id(), ['content' => $page->main(), 'type' => "main"]);
        $html = $this->elementparser($element);
        return $this->bodyparser($html);
    }

    /**
     * Used to convert the markdown user manual to html document
     *
     * @param string $text Input text in markdown
     * @return string html formated text
     */
    public function rendermanual(string $text): string
    {
        $text = $this->markdown($text);
        $text = $this->headerid($text, 1, 5, 'main', 0);
        return $text;
    }


    /**
     * Combine body and head to create html file
     *
     * @return string html string
     */
    private function gethmtl()
    {

        $body = $this->getbody($this->readbody());
        $parsebody = $this->bodyparser($body);
        $this->postprocessaction = $this->checkpostprocessaction($parsebody);
        $head = $this->gethead();

        $lang = !empty($this->page->lang()) ? $this->page->lang() : Config::lang();
        $langproperty = 'lang="' . $lang . '"';
        $html = "<!DOCTYPE html>\n<html $langproperty >\n<head>\n$head\n</head>";
        $html .= "\n$parsebody\n</html>\n";

        return $html;
    }


    private function readbody()
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
        $body = $this->automedialist($body);
        return $body;
    }


    /**
     * Analyse BODY, call the corresponding CONTENTs and render everything
     *
     * @param string $body as the string BODY of the page
     *
     * @return string as the full rendered BODY of the page
     */
    private function getbody(string $body): string
    {
        // Elements that can be detected
        $types = array_map("strtoupper", Model::HTML_ELEMENTS);

        // First level regex
        $regex = implode("|", $types);

        $matches = $this->match($body, $regex);

        // First, analyse the synthax and call the corresponding methods
        if (!empty($matches)) {
            foreach ($matches as $match) {
                $element = new Element($this->page->id(), $match);
                $element->setcontent($this->getelementcontent($element->sources(), $element->type()));
                $element->setcontent($this->elementparser($element));
                $body = str_replace($element->fullmatch(), $element->content(), $body);
            }
        }

        return $body;
    }

    /**
     * Return HEAD html element of a page
     */
    private function gethead(): string
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
        if (!empty($this->page->favicon()) && file_exists(Model::FAVICON_DIR . $this->page->favicon())) {
            $href = Model::faviconpath() . $this->page->favicon();
            $head .= "<link rel=\"shortcut icon\" href=\"$href\" type=\"image/x-icon\">";
        } elseif (!empty(Config::defaultfavicon()) && file_exists(Model::FAVICON_DIR . Config::defaultfavicon())) {
            $href = Model::faviconpath() . Config::defaultfavicon();
            $head .= "<link rel=\"shortcut icon\" href=\"$href\" type=\"image/x-icon\">";
        }
        $head .= "<meta name=\"description\" content=\"$description\" />\n";
        $head .= "<meta name=\"viewport\" content=\"width=device-width\" />\n";
        $head .= "<meta name=\"generator\" content=\"W-cms\" />\n";

        $head .= "<meta property=\"og:type\" content=\"website\" />";
        $head .= "<meta property=\"og:title\" content=\"$title$suffix\">\n";
        $head .= "<meta property=\"og:description\" content=\"$description\">\n";

        if (!empty($this->page->thumbnail())) {
            $content = Config::domain() . Model::thumbnailpath() . $this->page->thumbnail();
            $head .= "<meta property=\"og:image\" content=\"$content\">\n";
        } elseif (!empty(Config::defaultthumbnail())) {
            $content = Config::domain() . Model::thumbnailpath() . Config::defaultthumbnail();
            $head .= "<meta property=\"og:image\" content=\"$content\">\n";
        }

        $head .= "<meta property=\"og:url\" content=\"$url$id/\">\n";

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

        if (!empty($this->page->templatejavascript())) {
            $templatejspage = $this->page->templatejavascript();
            $head .= "<script src=\"$renderpath$templatejspage.js\" async/></script>\n";
        }
        if (!empty($this->page->javascript())) {
            $head .= "<script src=\"$renderpath$id.js\" async/></script>\n";
        }

        if (!empty(Config::analytics())) {
            $analitycs = Config::analytics();
            $head .= "\n
            <!-- Global site tag (gtag.js) - Google Analytics -->
			<script async src=\"https://www.googletagmanager.com/gtag/js?id=$analitycs\"></script>
			<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());

			gtag('config', 'Config::analytics()');
			</script>
			\n";
        }
        return $head;
    }

    /**
     * This create a HTML link for every stylsheet that are templated
     *
     * @param Page $page                    Page being rendered
     * @return string                       HTML to insert into <head> of page
     */
    private function recursivecss(Page $page): string
    {
        $head = "";
        try {
            $templates = $this->pagemanager->getpagecsstemplates($page);
            foreach ($templates as $template) {
                if (in_array('externalcss', $template->templateoptions())) {
                    foreach ($template->externalcss() as $externalcss) {
                        $head .= "<link href=\"$externalcss\" rel=\"stylesheet\" />\n";
                    }
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
     * Foreach $sources (pages), this will get the corresponding $type element content
     *
     * @param string[] $sources             Array of pages ID
     * @param string $type                  Type of element
     */
    private function getelementcontent(array $sources, string $type)
    {
        if (!in_array($type, Model::HTML_ELEMENTS)) {
            throw new InvalidArgumentException();
        }
        $content = '';
        $subseparator = "\n\n";
        foreach ($sources as $source) {
            if ($source !== $this->page->id()) {
                try {
                    $subcontent = $this->pagemanager->get($source)->$type();
                } catch (RuntimeException $e) {
                    $subcontent = $this->page->$type();
                }
            } else {
                $subcontent = $this->page->$type();
            }
            $content .= $subseparator . $subcontent;
        }
        return $content . $subseparator;
    }

    private function elementparser(Element $element)
    {
        $content = $element->content();
        $content = $this->automedialist($content);
        $content = $this->pageoptlist($content);
        $content = $this->randomopt($content);
        $content = $this->date($content);
        $content = $this->thumbnail($content);
        $content = $this->pageid($content);
        $content = $this->url($content);
        $content = $this->path($content);
        if ($element->everylink() > 0) {
            $content = $this->everylink($content, $element->everylink());
        }
        if ($element->markdown()) {
            $content = $this->markdown($content);
        }
        $content = $this->desctitle($content, $this->page->description(), $this->page->title());
        if ($element->headerid()) {
            $content = $this->headerid(
                $content,
                $element->minheaderid(),
                $element->maxheaderid(),
                $element->type(),
                $element->headeranchor()
            );
        }
        if ($element->urllinker()) {
            $content = $this->autourl($content);
        }
        if ($element->tag()) {
            $type = $element->type();
            $content = "\n<{$type}>\n{$content}\n</{$type}>\n";
        }

        return $content;
    }


    private function bodyparser(string $text)
    {
        $text = $this->media($text);

        $text = $this->summary($text);

        $text = $this->rss($text);

        $text = $this->authors($text);
        $text = $this->wikiurl($text);

        $text = "<body>\n$text\n</body>";
        $text = $this->htmlink($text);

        $text = $this->authenticate($text);

        return $text;
    }

    private function desctitle($text, $desc, $title)
    {
        $text = str_replace('%TITLE%', $title, $text);
        $text = str_replace('%DESCRIPTION%', $desc, $text);
        return $text;
    }
    /**
     * Search and replace referenced media code by full absolute address.
     * Add a target="_blank" attribute to link pointing to media.
     *
     * About the regex : it will match everything that do not start with a `./` a `/` or an URI sheme.
     * (`https:`, `ftps:`, `mailto:`, `matrix:` etc.) and contain at list one point `.`
     */
    private function media(string $text): string
    {
        $regex = '%href="(?!([/#]|[a-zA-Z\.\-\+]+:|\.+/))([^"]+\.[^";]+)"%';
        $text = preg_replace($regex, 'href="' . Model::mediapath() . '$2" target="_blank"', $text);
        $regex = '%src="(?!([/#]|[a-zA-Z\.\-\+]+:|\.+/))([^"]+\.[^";]+)"%';
        $text = preg_replace($regex, 'src="' . Model::mediapath() . '$2"', $text);
        return $text;
    }


    /**
     * Look for datas about pages.
     *
     * @param string $text the page text as html
     */
    private function richlink(string $text): string
    {
        $text = preg_replace('#<a(.*href="(https?:\/\/(.+))".*)>\2</a>#', "<a$1>$3</a>", $text);
        return $text;
    }

    /**
     * Replace plain URL with HTML link pointing to their address.
     *
     * This will also include `target=_blank` and `class=external` attributes.
     */
    private function autourl($text): string
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
     * Add `external` or `internal` class attribute in `<a>` anchor HTML link tags
     *
     * For internal link, indicate if page exist or not.
     * If it exist, add description in title and privacy as class.
     *
     * Keep existing class and remove duplicates or useless spaces in class attribute
     */
    private function htmlink(string $text): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        /** Force UTF-8 encoding for loadHTML by defining it in the content itself with an XML tag that need to be removed later */
        $text = '<?xml encoding="utf-8" ?>' . $text;
        /** @phpstan-ignore-next-line Error supposed to be thrown here but is'nt */
        $dom->loadHTML($text, LIBXML_NOERROR | LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
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
                if ($this->externallinkblank) {
                    $link->setAttribute('target', '_blank');
                    $link->setAttribute('class', implode(' ', array_unique($classes)));
                }
            } elseif (preg_match('~^([\w-]+)((\/?#[\w-]+)|(\/([\w\-\%\[\]\=\?\&]*)))?$~', $href, $out)) {
                $classes[] = 'internal';
                $fragment = $out[2] ?? '';
                $link->setAttribute('href', $this->upage($out[1]) . $fragment);
                if (isset($out[5]) && in_array($out[5], ['add', 'edit', 'update', 'render', 'download', 'delete'])) {
                    $classes[] = $out[5];
                }
                try {
                    $page = $this->pagemanager->get($out[1]);
                    $link->setAttribute('title', $page->description());
                    $classes[] = 'exist';
                    $classes[] = $page->secure('string');
                    $this->linkto[] = $page->id();
                } catch (RuntimeException $e) {
                    $link->setAttribute('title', Config::existnot());
                    $classes[] = 'existnot';
                    // TODO: store internal link that exist not in $this
                }
                if ($this->internallinkblank) {
                    $link->setAttribute('target', '_blank');
                }
                $link->setAttribute('class', implode(' ', array_unique($classes)));
            }
        }
        // By passing the documentElement to saveHTML, special chars are not converted to entities
        return $dom->saveHTML($dom->documentElement);
    }

    /**
     * Replace wiki links [[page_id]] with HTML link
     */
    private function wikiurl(string $text): string
    {
        $linkto = [];
        $rend = $this;
        $text = preg_replace_callback(
            '%\[\[([\w-]+)\/?(#[\w-]+)?\]\]%',
            function ($matches) use ($rend, &$linkto) {
                $target = $this->internallinkblank ? ' target="_blank"' : '';
                try {
                    $matchpage = $rend->pagemanager->get($matches[1]);
                    $fragment = $matches[2] ?? '';
                    $href = $rend->upage($matches[1]) . $fragment;
                    $t = $matchpage->description();
                    $c = 'internal exist ' . $matchpage->secure('string');
                    $a = $matchpage->title();
                    $linkto[] = $matchpage->id();
                } catch (RuntimeException $e) {
                    $href = $rend->upage($matches[1]);
                    $t = Config::existnot();
                    $c = 'internal existnot" ' . $target;
                    $a = $matches[1];
                }
                return '<a href="' . $href . '" title="' . $t . '" class="' . $c . '" ' . $target . ' >' . $a . '</a>';
            },
            $text
        );
        $this->linkto = array_unique(array_merge($this->linkto, $linkto));
        return $text;
    }

    /**
     * Add Id to html header elements and store the titles in the `$this->sum` var
     *
     * @param string $text Input html document to scan
     * @param int $min Maximum header deepness to look for. Min = 1 Max = 6 Default = 1
     * @param int $max Maximum header deepness to look for. Min = 1 Max = 6 Default = 6
     * @param string $element Name of element being analysed
     * @param int $anchormode Mode of anchor link display. see Element HEADERANCHORMODES
     *
     * @return string text with id in header
     */

    private function headerid(string $text, int $min, int $max, string $element, int $anchormode): string
    {
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

    private function markdown($text)
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
    private function match(string $text, string $include): array
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
    private function automedialist(string $text): string
    {
        $matches = $this->match($text, 'MEDIA');

        if (!empty($matches)) {
            foreach ($matches as $match) {
                $medialist = new Mediaoptlist($match);
                $medialist->readoptions();
                $text = str_replace($medialist->fullmatch(), $medialist->generatecontent(), $text);
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
    private function summary(string $text): string
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
    private function pageoptlist(string $text): string
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
                $this->linkto = array_merge($this->linkto, array_keys($pagetable));
                $content = $optlist->listhtml($pagetable, $this, $this->page);
                $text = str_replace($match['fullmatch'], $content, $text);
            } catch (RuntimeException $e) {
                Logger::errorex($e);
            }
        }

        return $text;
    }

    /**
     * Render Random links
     */
    private function randomopt(string $text): string
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
    private function rss(string $text): string
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
    private function rssmatch(string $text): array
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



    private function date(string $text): string
    {
        $date = Clock::DATE;
        $time = Clock::TIME;
        $matches = $this->match($text, "$date|$time");
        $searches = [];
        $replaces = [];
        foreach ($matches as $match) {
            $clock = new Clock(
                $match['type'],
                $this->page->date(),
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
    private function thumbnail(string $text): string
    {
        $src = Model::thumbnailpath() . $this->page->thumbnail();
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
    private function pageid(string $text): string
    {
        return str_replace(['%PAGEID%', '%ID%'], $this->page->id(), $text);
    }

    /**
     * Replace each occurence of `%URL%` with page ID
     * @param string $text input text
     * @return string output text with replaced elements
     */
    private function url(string $text): string
    {
        return str_replace('%URL%', Config::domain() . $this->upage($this->page->id()), $text);
    }

    /**
     * Replace each occurence of `%PATH%` with page path
     * @param string $text input text
     * @return string output text with replaced elements
     */
    private function path(string $text): string
    {
        return str_replace('%PATH%', $this->upage($this->page->id()), $text);
    }

    /**
     * Replace `%AUTHORS%` with a rendered list of authors
     */
    private function authors(string $text): string
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
    private function checkpostprocessaction(string $text): bool
    {
        $counterpaterns = Servicepostprocess::COUNTERS;
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
    private function everylink(string $text, int $limit): string
    {
        $regex = '~([\w\-_éêèùïüîçà]{' . $limit . ',})(?![^<]*>|[^<>]*<\/)~';
        $text = preg_replace_callback($regex, function ($matches) {
            return '<a href="' . Model::idclean($matches[1]) . '">' . $matches[1] . '</a>';
        }, $text);
        return $text;
    }



    /**
     * @param string $text content to analyse and replace
     *
     * @return string text ouput
     */
    private function authenticate(string $text): string
    {
        $id = $this->page->id();
        $regex = '~\%CONNECT(\?dir=([a-zA-Z0-9-_]+))?\%~';
        $text = preg_replace_callback($regex, function ($matches) use ($id) {
            if (isset($matches[2])) {
                $id = $matches[2];
            }
            $form = '<form action="' . Model::dirtopath('!co') . '" method="post">
            <input type="text" name="user" id="loginuser" autofocus placeholder="user" required>
			<input type="password" name="pass" id="loginpass" placeholder="password" required>
			<input type="hidden" name="route" value="pageread">
			<input type="hidden" name="id" value="' . $id . '">
			<input type="submit" name="log" value="login" id="button">
			</form>';
            return $form;
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
     * Render a list of Users as a HTML <ul> that may contain links
     *
     * @param User[] $users     List of User
     * @return string           List of user in HTML
     */
    private function userlist(array $users): string
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
        $linkto = $this->linkto;
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
