<?php

namespace Wcms;

use Exception;
use LogicException;
use Michelf\MarkdownExtra;

class Modelrender extends Modelpage
{
    /** @var \AltoRouter */
    protected $router;
    /** @var Page Actual page being rendered*/
    protected $page;
    protected $linkto = [];
    protected $sum = [];
    protected $internallinkblank = '';
    protected $externallinkblank = '';

    public function __construct(\AltoRouter $router)
    {
        parent::__construct();

        $this->router = $router;
        $this->pagelist = $this->pagelist();

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
        $text = $this->headerid($text, 1, 5);
        return $text;
    }


    /**
     * Generate page relative link for given page_id including basepath
     *
     * @param string $id given page ID
     * @return string Relative URL
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
    public function gethmtl()
    {

        $head = $this->gethead();
        $body = $this->getbody($this->readbody());
        $parsebody = $this->bodyparser($body);

        $lang = !empty($this->page->lang()) ? $this->page->lang() : Config::lang();
        $langproperty = 'lang="' . $lang . '"';
        $html = "<!DOCTYPE html>\n<html $langproperty >\n<head>\n$head\n</head>\n$parsebody\n</html>";

        return $html;
    }


    public function readbody()
    {
        if (!empty($this->page->templatebody())) {
            $templateid = $this->page->templatebody();
            $templatepage = $this->get($templateid);
            if ($templatepage !== false) {
                $body = $templatepage->body();
            } else {
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
    public function getbody(string $body): string
    {
        // Elements that can be detected
        $types = ['HEADER', 'NAV', 'MAIN', 'ASIDE', 'FOOTER'];

        // First level regex
        $regex = implode("|", $types);

        $matches = $this->match($body, $regex);

        // First, analyse the synthax and call the corresponding methods
        if (!empty($matches)) {
            foreach ($matches as $key => $match) {
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
     * @param array $sources Array of pages ID
     * @param string $type Type of element
     */
    public function getelementcontent(array $sources, string $type)
    {
        $content = '';
        $subseparator = PHP_EOL . PHP_EOL;
        foreach ($sources as $source) {
            if ($source !== $this->page->id()) {
                $subcontent = $this->getpageelement($source, $type);
                if ($subcontent !== false) {
                    if (empty($subcontent)) {
                        $message = 'The ' . strtoupper($type) . ' from page "' . $source . '" is currently empty !';
                        $subcontent = "\n<!-- ' . $message . ' -->\n";
                    }
                } else {
                    $read = '<h2>Rendering error :</h2>';
                    $read .= '<p>The page <strong><code>' . $source . '</code></strong>, does not exist yet.</p>';
                    //throw new Exception($read);
                }
            } else {
                $subcontent = $this->page->$type();
            }
            $content .= $subseparator . $subcontent;
        }
        return $content . $subseparator;
    }

    public function elementparser(Element $element)
    {
        $content = $this->article($element->content());
        $content = $this->automedialist($content);
        $content = $this->pageoptlist($content);
        $content = $this->date($content);
        $content = $this->thumbnail($content);
        if ($element->autolink()) {
            $content = $this->everylink($content, $element->autolink());
        }
        if ($element->markdown()) {
            $content = $this->markdown($content);
        }
        $content = $this->desctitle($content, $this->page->description(), $this->page->title());
        if ($element->headerid()) {
            $content = $this->headerid($content, $element->minheaderid(), $element->maxheaderid(), $element->type());
        }

        return $content;
    }


    /**
     * Write css javascript and html as files in the assets folder
     */
    public function write(string $html)
    {
        self::writefile(self::HTML_RENDER_DIR . $this->page->id() . '.html', $html);
        self::writefile(self::RENDER_DIR . $this->page->id() . '.css', $this->page->css());
        //self::writefile(self::RENDER_DIR . $this->page->id() . '.quick.css', $this->page->quickcss());
        self::writefile(self::RENDER_DIR . $this->page->id() . '.js', $this->page->javascript());
    }



    public function writetemplates()
    {
        if (array_key_exists('css', $this->page->template())) {
            $tempaltecsspage = $this->get($this->page->template()['css']);
            self::writefile(Model::RENDER_DIR . $tempaltecsspage->id() . '.css', $tempaltecsspage->css());
        }
        if (array_key_exists('javascript', $this->page->template())) {
            $templatejspage = $this->get($this->page->template()['javascript']);
            self::writefile(Model::RENDER_DIR . $templatejspage->id() . '.js', $templatejspage->javascript());
        }
    }




    public function gethead()
    {
        $id = $this->page->id();
        $globalpath = Model::dirtopath(Model::CSS_DIR);
        $renderpath = Model::renderpath();
        $description = $this->page->description();
        $title = $this->page->title();
        $url = Config::url();

        $head = '';

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


        foreach ($this->page->externalcss() as $externalcss) {
            $head .= "<link href=\"$externalcss\" rel=\"stylesheet\" />\n";
        }

        if (!empty($this->page->templatecss() && in_array('externalcss', $this->page->templateoptions()))) {
            $templatecss = $this->get($this->page->templatecss());
            if ($templatecss !== false) {
                foreach ($templatecss->externalcss() as $externalcss) {
                    $head .= "<link href=\"$externalcss\" rel=\"stylesheet\" />\n";
                }
            }
        }

        $head .= PHP_EOL . $this->page->customhead() . PHP_EOL;

        if (file_exists(self::GLOBAL_CSS_FILE)) {
            $head .= "<link href=\"{$globalpath}global.css\" rel=\"stylesheet\" />\n";
        }

        if (!empty($this->page->templatecss())) {
            $tempaltecsspage = $this->page->templatecss();
            $head .= "<link href=\"$renderpath$tempaltecsspage.css\" rel=\"stylesheet\" />\n";
        }
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

        if (!empty($this->page->redirection())) {
            if (preg_match('%https?:\/\/\S*%', $this->page->redirection(), $out)) {
                $url = $out[0];
            } elseif (key_exists($this->page->redirection(), $this->pagelist())) {
                $url = $this->upage($this->page->redirection());
            }
            $head .= "\n<meta http-equiv=\"refresh\" content=\"{$this->page->refresh()}; URL=$url\" />";
        }


        return $head;
    }

    public function desctitle($text, $desc, $title)
    {
        $text = str_replace('%TITLE%', $title, $text);
        $text = str_replace('%DESCRIPTION%', $desc, $text);
        return $text;
    }


    public function bodyparser(string $text)
    {
        $text = $this->media($text);

        $text = $this->summary($text);

        $text = $this->wurl($text);
        $text = $this->wikiurl($text);



        $text = str_replace('href="http', "class=\"external\" $this->externallinkblank href=\"http", $text);

        $text = $this->shortenurl($text);

        $text = $this->autourl($text);

        $text = $this->authenticate($text);

        return $text;
    }

    public function media(string $text): string
    {
        $regex = '%(src|href)="([\w\-]+(\/([\w\-])+)*\.[a-z0-9]{1,5})"%';
        $text = preg_replace($regex, '$1="' . Model::mediapath() . '$2" target="_blank"', $text);
        if (!is_string($text)) {
            //throw new Exception('Rendering error -> media module');
        }
        return $text;
    }

    /**
     * Shorten the urls of links whose content equals the href.
     *
     * @param string $text the page text as html
     */
    public function shortenurl(string $text): string
    {
        $text = preg_replace('#<a(.*href="(https?:\/\/(.+))".*)>\2</a>#', "<a$1>$3</a>", $text);
        return $text;
    }


    public function autourl($text)
    {
        $text = preg_replace(
            '#( |\R|(>)|(&lt;))(https?:\/\/(\S+\.[^< ]+))(((?(3)&gt;|))(?(2)</[^a]|))#',
            "$1<a href=\"$4\" class=\"external\" $this->externallinkblank>$4</a>$6",
            $text
        );
        return $text;
    }

    public function wurl(string $text)
    {
        $linkto = [];
        $rend = $this;
        $text = preg_replace_callback(
            '%href="([\w-]+)\/?(#?[a-z-_]*)"%',
            function ($matches) use ($rend, &$linkto) {
                $matchpage = $rend->get($matches[1]);
                if (!$matchpage) {
                    $href = $rend->upage($matches[1]);
                    $t = Config::existnot();
                    $c = 'internal existnot"' . $this->internallinkblank;
                } else {
                    $href = $rend->upage($matches[1]) . $matches[2];
                    $t = $matchpage->description();
                    $c = 'internal exist ' . $matchpage->secure('string');
                    $linkto[] = $matchpage->id();
                }
                $link =  'href="' . $href . '" title="' . $t . '" class="' . $c . '"' . $this->internallinkblank;
                return $link;
            },
            $text
        );
        $this->linkto = array_unique(array_merge($this->linkto, $linkto));
        return $text;
    }

    public function wikiurl(string $text)
    {
        $linkto = [];
        $rend = $this;
        $text = preg_replace_callback(
            '%\[([\w-]+)\/?#?([a-z-_]*)\]%',
            function ($matches) use ($rend, &$linkto) {
                $matchpage = $rend->get($matches[1]);
                if (!$matchpage) {
                    $href = $rend->upage($matches[1]);
                    $t = Config::existnot();
                    $c = 'internal existnot" ' . $this->internallinkblank;
                    $a = $matches[1];
                } else {
                    $href = $rend->upage($matches[1]) . $matches[2];
                    $t = $matchpage->description();
                    $c = 'internal exist ' . $matchpage->secure('string');
                    $a = $matchpage->title();
                    $linkto[] = $matchpage->id();
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

    public function headerid(string $text, int $min = 1, int $max = 6, string $element = 'body'): string
    {
        if ($min > 6 || $min < 1) {
            $min = 6;
        }
        if ($max > 6 || $max < 1) {
            $max = 6;
        }

        $text = preg_replace_callback(
            "/<h([$min-$max])((.*)id=\"([^\"]*)\"(.*)|.*)>(.+)<\/h[$min-$max]>/mU",
            function ($matches) {
                $level = $matches[1];
                $beforeid = $matches[3];
                $id = $matches[4];
                $afterid = $matches[5];
                $content = $matches[6];
                // if no custom id is defined, use idclean of the content as id
                if (empty($id)) {
                    $id = idclean($content);
                }
                $this->sum[] = new Header($id, intval($level), $content);
                return "<h$level $beforeid id=\"$id\" $afterid>$content</h$level>";
            },
            $text
        );
        return $text;
    }

    public function markdown($text)
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



    public function article($text)
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
     * @return array $matches Ordered array containing an array of `fullmatch` and `filter`
     */
    public function match(string $text, string $include): array
    {
        preg_match_all('~\%(' . $include . ')(\?([a-zA-Z0-9\[\]\&=\-_\/\%\+\*\;]*))?\%~', $text, $out);

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
    public function automedialist(string $text): string
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
    public function summary(string $text): string
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
    public function pageoptlist(string $text): string
    {
        $matches = $this->match($text, 'LIST');

        $modelhome = new Modelhome();

        if (!empty($matches)) {
            foreach ($matches as $match) {
                $optlist = new Optlist(['render' => $this]);
                $optlist->parsehydrate($match['options']);
                $pagetable = $modelhome->pagetable($this->pagelist(), $optlist, '', []);
                $content = $optlist->listhtml($pagetable, $this->page, $this);
                $text = str_replace($match['fullmatch'], $content, $text);
            }
        }
        return $text;
    }



    public function date(string $text): string
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
    public function thumbnail(string $text): string
    {
        $src = Model::thumbnailpath() . $this->page->thumbnail();
        $alt = $this->page->title();
        $img = '<img class="thumbnail" src="' . $src . '" alt="' . $alt . '">';
        $img = PHP_EOL . $img . PHP_EOL;
        $text = str_replace('%THUMBNAIL%', $img, $text);

        return $text;
    }

    /**
     * Autolink Function : transform every word of more than $limit characters in internal link
     *
     * @param string $text The input text to be converted
     *
     * @return string Conversion output
     */
    public function everylink(string $text, int $limit): string
    {
        $regex = '~([\w-_éêèùïüîçà]{' . $limit . ',})(?![^<]*>|[^<>]*<\/)~';
        $text = preg_replace_callback($regex, function ($matches) {
            return '<a href="' . idclean($matches[1]) . '">' . $matches[1] . '</a>';
        }, $text);
        return $text;
    }



    /**
     * @param string $text content to analyse and replace
     *
     * @return string text ouput
     */
    public function authenticate(string $text): string
    {
        $id = $this->page->id();
        $regex = '~\%CONNECT(\?dir=([a-zA-Z0-9-_]+))?\%~';
        $text = preg_replace_callback($regex, function ($matches) use ($id) {
            if (isset($matches[2])) {
                $id = $matches[2];
            }
            $form = '<form action="' . $this->dirtopath('!co') . '" method="post">
			<input type="password" name="pass" id="loginpass" placeholder="password">
			<input type="hidden" name="route" value="pageread/">
			<input type="hidden" name="id" value="' . $id . '">
			<input type="submit" name="log" value="login" id="button">
			</form>';
            return $form;
        }, $text);
        return $text;
    }




    public function linkto()
    {
        sort($this->linkto);
        $linkto = $this->linkto;
        $this->linkto = [];
        return $linkto;
    }


    // _________________________ G E T ___________________________________

    public function sum()
    {
        return $this->sum;
    }
}
