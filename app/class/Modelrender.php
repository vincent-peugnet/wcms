<?php

namespace Wcms;

use AltoRouter;
use Exception;
use Http\Discovery\Exception\NotFoundException;
use InvalidArgumentException;
use LogicException;
use Michelf\MarkdownExtra;
use RuntimeException;

class Modelrender extends Modelpage
{
    /** @var AltoRouter */
    protected ?AltoRouter $router;

    /**
     * @var Page                            Actual page being rendered
     * */
    protected $page;

    protected $linkto = [];
    protected $sum = [];
    protected $internallinkblank = '';
    protected $externallinkblank = '';

    /**
     * @var Bookmark[]                      Associative array of Bookmarks using fullmatch as key
     * */
    protected $rsslist = [];

    /**
     * @param AltoRouter $router            Router used to generate urls
     * @param Page[] $pagelist              Optionnal : if pagelist already exist, feed it with it
     */
    public function __construct(AltoRouter $router, array $pagelist = [])
    {
        parent::__construct();

        $this->router = $router;
        $this->pagelist = empty($pagelist) ? [] : $pagelist;

        if (Config::internallinkblank()) {
            $this->internallinkblank = ' target="_blank" ';
        }

        if (Config::externallinkblank()) {
            $this->externallinkblank = ' target="_blank" ';
        }
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
        $text = $this->headerid($text, 1, 5, 'main', false);
        return $text;
    }


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
            return $this->router->generate('pageread/', ['page' => $id]);
        } catch (Exception $e) {
            throw new LogicException($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * Main function
     *
     * @param Page $page page to render
     *
     * @throws Filesystemexception          If wrinting render files fails
     */
    public function render(Page $page)
    {
        $this->page = $page;

        $this->write($this->gethmtl());
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
        $head = $this->gethead();

        $lang = !empty($this->page->lang()) ? $this->page->lang() : Config::lang();
        $langproperty = 'lang="' . $lang . '"';
        $html = "<!DOCTYPE html>\n<html $langproperty >\n<head>\n$head\n</head>\n$parsebody\n</html>";

        return $html;
    }


    private function readbody()
    {
        if (!empty($this->page->templatebody())) {
            $templateid = $this->page->templatebody();
            try {
                $body = $this->get($templateid)->body();
            } catch (RuntimeException $e) {
                Logger::errorex($e);
                $body = $this->page->body();
                $this->page->settemplatebody('');
            }
        } else {
            $body = $this->page->body();
        }
        $body = $this->article($body);
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
                $element->addtags();
                $body = str_replace($element->fullmatch(), $element->content(), $body);
            }
        }



        return $body;
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
        $subseparator = PHP_EOL . PHP_EOL;
        foreach ($sources as $source) {
            if ($source !== $this->page->id()) {
                try {
                    $subcontent = $this->get($source)->$type();
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
        $content = $this->article($element->content());
        $content = $this->automedialist($content);
        $content = $this->pageoptlist($content);
        $content = $this->date($content);
        $content = $this->thumbnail($content);
        $content = $this->pageid($content);
        $content = $this->url($content);
        $content = $this->path($content);
        if ($element->autolink()) {
            $content = $this->everylink($content, $element->autolink());
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

        return $content;
    }


    /**
     * Write css javascript and html as files in the assets folder
     *
     * @throws Filesystemexception
     */
    private function write(string $html)
    {
        Fs::writefile(self::HTML_RENDER_DIR . $this->page->id() . '.html', $html);
        Fs::writefile(self::RENDER_DIR . $this->page->id() . '.css', $this->page->css(), 0664);
        //Fs::writefile(self::RENDER_DIR . $this->page->id() . '.quick.css', $this->page->quickcss());
        Fs::writefile(self::RENDER_DIR . $this->page->id() . '.js', $this->page->javascript(), 0664);
    }



    /**
     * Return HEAD html element of a page
     */
    private function gethead(): string
    {
        $id = $this->page->id();
        $globalpath = Model::dirtopath(Model::CSS_DIR);
        $renderpath = Model::renderpath();
        $description = $this->page->description();
        $title = $this->page->title();
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
        $head .= "<title>$title</title>\n";
        if (!empty($this->page->favicon()) && file_exists(self::FAVICON_DIR . $this->page->favicon())) {
            $href = Model::faviconpath() . $this->page->favicon();
            $head .= "<link rel=\"shortcut icon\" href=\"$href\" type=\"image/x-icon\">";
        } elseif (!empty(Config::defaultfavicon()) && file_exists(self::FAVICON_DIR . Config::defaultfavicon())) {
            $href = Model::faviconpath() . Config::defaultfavicon();
            $head .= "<link rel=\"shortcut icon\" href=\"$href\" type=\"image/x-icon\">";
        }
        $head .= "<meta name=\"description\" content=\"$description\" />\n";
        $head .= "<meta name=\"viewport\" content=\"width=device-width\" />\n";
        $head .= "<meta name=\"generator\" content=\"W-cms\" />\n";

        $head .= "<meta property=\"og:type\" content=\"website\" />";
        $head .= "<meta property=\"og:title\" content=\"$title\">\n";
        $head .= "<meta property=\"og:description\" content=\"$description\">\n";

        if (!empty($this->page->thumbnail())) {
            $content = Config::domain() . self::thumbnailpath() . $this->page->thumbnail();
            $head .= "<meta property=\"og:image\" content=\"$content\">\n";
        } elseif (!empty(Config::defaultthumbnail())) {
            $content = Config::domain() . self::thumbnailpath() . Config::defaultthumbnail();
            $head .= "<meta property=\"og:image\" content=\"$content\">\n";
        }

        $head .= "<meta property=\"og:url\" content=\"$url$id/\">\n";

        foreach ($this->rsslist as $bookmark) {
            $atompath = Servicerss::atompath($bookmark->id());
            $title = $bookmark->name();
            $head .= "<link href=\"$atompath\" type=\"application/atom+xml\" rel=\"alternate\" title=\"$title\" />";
        }

        $head .= PHP_EOL . $this->page->customhead() . PHP_EOL;

        foreach ($this->page->externalcss() as $externalcss) {
            $head .= "<link href=\"$externalcss\" rel=\"stylesheet\" />\n";
        }

        if (file_exists(self::GLOBAL_CSS_FILE)) {
            $head .= "<link href=\"{$globalpath}global.css\" rel=\"stylesheet\" />\n";
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
            $templates = $this->getpagecsstemplates($page);
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

    private function desctitle($text, $desc, $title)
    {
        $text = str_replace('%TITLE%', $title, $text);
        $text = str_replace('%DESCRIPTION%', $desc, $text);
        return $text;
    }


    private function bodyparser(string $text)
    {
        $text = $this->media($text);

        $text = $this->summary($text);

        $text = $this->rss($text);

        $text = $this->authors($text);
        $text = $this->wurl($text);
        $text = $this->wikiurl($text);



        $text = str_replace('href="http', "class=\"external\" $this->externallinkblank href=\"http", $text);

        $text = $this->shortenurl($text);

        $text = $this->autourl($text);

        $text = $this->authenticate($text);

        return $text;
    }

    /**
     * Search and replace referenced media code by full absolute address.
     * Add a target="_blank" attribute to link pointing to media.
     */
    private function media(string $text): string
    {
        $regex = '%href="([\w\-]+(\/([\w\-])+)*\.[a-z0-9]{1,5})"%';
        $text = preg_replace($regex, 'href="' . Model::mediapath() . '$1" target="_blank"', $text);
        $regex = '%src="([\w\-]+(\/([\w\-])+)*\.[a-z0-9]{1,5})"%';
        $text = preg_replace($regex, 'src="' . Model::mediapath() . '$1"', $text);
        return $text;
    }

    /**
     * Shorten the urls of links whose content equals the href.
     *
     * @param string $text the page text as html
     */
    private function shortenurl(string $text): string
    {
        $text = preg_replace('#<a(.*href="(https?:\/\/(.+))".*)>\2</a>#', "<a$1>$3</a>", $text);
        return $text;
    }


    private function autourl($text)
    {
        $text = preg_replace(
            '#( |\R|(>)|(&lt;))(https?:\/\/(\S+\.[^< ]+))(((?(3)&gt;|))(?(2)</[^a]|))#',
            "$1<a href=\"$4\" class=\"external\" $this->externallinkblank>$4</a>$6",
            $text
        );
        return $text;
    }

    private function wurl(string $text)
    {
        $linkto = [];
        $rend = $this;
        $text = preg_replace_callback(
            '%href="([\w-]+)\/?(#?[\w-]*)"%',
            function ($matches) use ($rend, &$linkto) {
                try {
                    $matchpage = $rend->get($matches[1]);
                    $href = $rend->upage($matches[1]) . $matches[2];
                    $t = $matchpage->description();
                    $c = 'internal exist ' . $matchpage->secure('string');
                    $linkto[] = $matchpage->id();
                } catch (RuntimeException $e) {
                    $href = $rend->upage($matches[1]);
                    $t = Config::existnot();
                    $c = 'internal existnot"' . $this->internallinkblank;
                }
                $link =  'href="' . $href . '" title="' . $t . '" class="' . $c . '"' . $this->internallinkblank;
                return $link;
            },
            $text
        );
        $this->linkto = array_unique(array_merge($this->linkto, $linkto));
        return $text;
    }

    private function wikiurl(string $text)
    {
        $linkto = [];
        $rend = $this;
        $text = preg_replace_callback(
            '%\[([\w-]+)\/?#?([a-z-_]*)\]%',
            function ($matches) use ($rend, &$linkto) {
                try {
                    $matchpage = $rend->get($matches[1]);
                    $href = $rend->upage($matches[1]) . $matches[2];
                    $t = $matchpage->description();
                    $c = 'internal exist ' . $matchpage->secure('string');
                    $a = $matchpage->title();
                    $linkto[] = $matchpage->id();
                } catch (RuntimeException $e) {
                    $href = $rend->upage($matches[1]);
                    $t = Config::existnot();
                    $c = 'internal existnot" ' . $this->internallinkblank;
                    $a = $matches[1];
                }
                $i = $this->internallinkblank;
                return '<a href="' . $href . '" title="' . $t . '" class="' . $c . '" ' . $i . ' >' . $a . '</a>';
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
     *
     * @return string text with id in header
     */

    private function headerid(string $text, int $min, int $max, string $element, bool $anchor): string
    {
        if ($min > 6 || $min < 1) {
            $min = 6;
        }
        if ($max > 6 || $max < 1) {
            $max = 6;
        }

        $text = preg_replace_callback(
            "/<h([$min-$max])((.*)id=\"([^\"]*)\"(.*)|.*)>(.+)<\/h[$min-$max]>/mU",
            function ($matches) use ($element, $anchor) {
                $level = $matches[1];
                $beforeid = $matches[3];
                $id = $matches[4];
                $afterid = $matches[5];
                $content = $matches[6];
                // if no custom id is defined, use idclean of the content as id
                if (empty($id)) {
                    $id = self::idclean($content);
                }
                $this->sum[$element][] = new Header($id, intval($level), $content);
                if ($anchor) {
                    $content = "<a href=\"#$id\">$content</a>";
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



    private function article($text)
    {
        $pattern = '/(\R\R|^\R|^)[=]{3,}([\w-]*)\R\R(.*)(?=\R\R[=]{3,}[\w-]*\R)/sUm';
        $text = preg_replace_callback($pattern, function ($matches) {
            if (!empty($matches[2])) {
                $id = ' id="' . $matches[2] . '" ';
            } else {
                $id = ' ';
            }
            return "<article $id markdown=\"1\" >\n\n$matches[3]\n\n</article>\n\n";
        }, $text);
        $text = preg_replace('/\R\R[=]{3,}([\w-]*)\R/', '', $text);
        return $text;
    }

    /**
     * Match `%INCLUDE?params=values&...%`
     *
     * @param string $text Input text to scan
     * @param string $include word to match
     *
     * @return array Ordered array containing an array of `fullmatch` and `options`
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
                $medialist = new Mediaopt($match);
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

        if (!empty($matches)) {
            foreach ($matches as $match) {
                $optlist = new Optlist(['render' => $this]);
                $optlist->parsehydrate($match['options']);
                $pagetable = $this->pagetable($this->pagelist(), $optlist, '', []);
                $this->linkto = array_merge($this->linkto, array_keys($pagetable));
                $content = $optlist->listhtml($pagetable, $this->page, $this);
                $text = str_replace($match['fullmatch'], $content, $text);
            }
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
        $page = $this->page;
        $text = preg_replace_callback('~\%DATE\%~', function ($matches) use ($page) {
            return '<time datetime=' . $page->date('string') . '>' . $page->date('dmy') . '</time>';
        }, $text);
        $text = preg_replace_callback('~\%TIME\%~', function ($matches) use ($page) {
            return '<time datetime=' . $page->date('string') . '>' . $page->date('ptime') . '</time>';
        }, $text);

        return $text;
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
        $img = PHP_EOL . $img . PHP_EOL;
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
     * Autolink Function : transform every word of more than $limit characters in internal link
     *
     * @param string $text The input text to be converted
     *
     * @return string Conversion output
     */
    private function everylink(string $text, int $limit): string
    {
        $regex = '~([\w-_éêèùïüîçà]{' . $limit . ',})(?![^<]*>|[^<>]*<\/)~';
        $text = preg_replace_callback($regex, function ($matches) {
            return '<a href="' . self::idclean($matches[1]) . '">' . $matches[1] . '</a>';
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
            $form = '<form action="' . $this->dirtopath('!co') . '" method="post">
            <input type="text" name="user" id="loginuser" autofocus placeholder="user" required>
			<input type="password" name="pass" id="loginpass" placeholder="password" required>
			<input type="hidden" name="route" value="pageread/">
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




    public function linkto()
    {
        sort($this->linkto);
        $linkto = $this->linkto;
        $key = array_search($this->page->id(), $linkto);
        if ($key) {
            unset($linkto[$key]);
        }
        $this->linkto = [];
        return $linkto;
    }


    // _________________________ R S S ___________________________________

    /**
     * @return string HTML Parsed MAIN content of a page
     * @todo render absolute media links
     */
    public function rsscontent(Page $page): string
    {
        $this->page = $page;
        $element = new Element($page->id(), ['content' => $page->main(), 'type' => "main"]);
        $html = $this->elementparser($element);
        return $this->bodyparser($html);
    }


    // _________________________ G E T ___________________________________

    public function sum()
    {
        return $this->sum;
    }

    public function router(): AltoRouter
    {
        return $this->router;
    }
}
