<?php

namespace Wcms;

use AltoRouter;
use DateTimeImmutable;
use DateTimeInterface;
use DomainException;
use Exception;
use IntlDateFormatter;
use InvalidArgumentException;
use League\Plates\Engine;
use RuntimeException;
use Wcms\Exception\Database\Notfoundexception;
use Wcms\Exception\Databaseexception;

class Controller
{
    protected Servicesession $servicesession;

    protected Workspace $workspace;

    /** @var User */
    protected $user;

    /** @var Altorouter */
    protected $router;

    /** @var Modeluser */
    protected $usermanager;

    /** @var Modelpage */
    protected $pagemanager;

    protected Engine $plates;

    /** @var DateTimeImmutable */
    protected $now;


    public const FLASH_MESSAGE_TYPES = [
        self::FLASH_INFO    => 1,
        self::FLASH_WARNING => 2,
        self::FLASH_SUCCESS => 3,
        self::FLASH_ERROR   => 4,
    ];

    public const FLASH_INFO     = 'info';
    public const FLASH_WARNING  = 'warning';
    public const FLASH_SUCCESS  = 'success';
    public const FLASH_ERROR    = 'error';


    public function __construct(AltoRouter $router)
    {
        $this->servicesession = new Servicesession();
        $this->workspace = $this->servicesession->getworkspace();
        $this->usermanager = new Modeluser();

        $this->user = new User();
        $this->setuser();
        $this->router = $router;
        $this->pagemanager = new Modelpage(Config::pagetable());
        $this->initplates();
        $this->now = new DateTimeImmutable("now", timezone_open("Europe/Paris"));
    }

    protected function setuser(): void
    {
        // check session, then cookies
        if (!is_null($this->servicesession->getuser())) {
            $sessionuser = $this->servicesession->getuser();
            try {
                $this->user = $this->usermanager->get($sessionuser);
            } catch (Notfoundexception $e) {
                Logger::warning("Deleted session using non existing user : '$sessionuser'");
                $this->servicesession->empty(); // empty the session as a non existing user was set
            }
        } elseif (!empty($_COOKIE['rememberme'])) {
            try {
                $modelconnect = new Modelconnect();
                $datas = $modelconnect->checkcookie();
                $user = $this->usermanager->get($datas['userid']);
                if ($user->checksession($datas['wsession'])) {
                    $this->user = $user;
                    $this->servicesession->setwsessionid($datas['wsession']);
                    $this->servicesession->setuser($user->id());
                } else {
                    $modelconnect->deleteauthcookie(); // As not listed in the user
                }
            } catch (Notfoundexception $e) {
                Logger::warning('Deleted auth cookie using non existing user');
                $modelconnect->deleteauthcookie(); // Delete auth cookie as a non existing user was set
            } catch (RuntimeException $e) {
                $this->sendflashmessage("Invalid Autentification cookie exist : $e", self::FLASH_WARNING);
            }
        }
    }

    public function initplates(): void
    {
        $formatershort = new IntlDateFormatter(Config::lang(), IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);
        $formatermedium = new IntlDateFormatter(Config::lang(), IntlDateFormatter::MEDIUM, IntlDateFormatter::MEDIUM);
        $this->plates = new Engine(Model::TEMPLATES_DIR);
        $this->plates->registerFunction('url', function (string $string, array $vars = [], string $get = '') {
            return $this->generate($string, $vars, $get);
        });
        $this->plates->registerFunction('upage', function (string $string, string $id) {
            return $this->generate($string, ['page' => $id]);
        });
        $this->plates->registerFunction('ubookmark', function (string $string, string $id) {
            return $this->generate($string, ['bookmark' => $id]);
        });
        $this->plates->registerFunction('caneditpage', function (Page $page) {
            return $this->canedit($page);
        });
        $this->plates->registerFunction('candeletepage', function (Page $page) {
            return $this->candelete($page);
        });
        $this->plates->registerFunction('dateshort', function (DateTimeInterface $date) use ($formatershort) {
            return $formatershort->format($date);
        });
        $this->plates->registerFunction('datemedium', function (DateTimeInterface $date) use ($formatermedium) {
            return $formatermedium->format($date);
        });
        $this->plates->addData(['flashmessages' => $this->getflashmessages()]);
    }

    /**
     *
     */
    public function showtemplate(string $template, array $params = []): void
    {
        $params = array_merge($this->commonsparams(), $params);
        echo $this->plates->render($template, $params);
    }

    /**
     * @return mixed[]
     */
    public function commonsparams(): array
    {
        $commonsparams = [];
        $commonsparams['user'] = $this->user;
        $commonsparams['pagelist'] = $this->pagemanager->list();
        $commonsparams['css'] = Model::assetscsspath();
        $commonsparams['now'] = new DateTimeImmutable();
        $commonsparams['workspace'] = $this->workspace;
        return $commonsparams;
    }



    /**
     * Generate the URL for a named route. Replace regexes with supplied parameters.
     *
     * @param string $route The name of the route.
     * @param array $params Associative array of parameters to replace placeholders with.
     * @param string $get Optionnal query GET parameters formated
     * @return string The URL of the route with named parameters in place.
     * @throws InvalidArgumentException If the route does not exist.
     */
    public function generate(string $route, array $params = [], string $get = ''): string
    {
        try {
            return $this->router->generate($route, $params) . $get;
        } catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Redirect to URL and send 302 code
     * @param string $url to redirect to
     */
    public function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    public function routedirect(string $route, array $vars = [], $gets = []): void
    {

        $get = empty($gets) ? "" : "?" . http_build_query($gets);
        $this->redirect($this->generate($route, $vars, $get));
    }

    public function error(int $code): never
    {
        http_response_code($code);
        exit;
    }

    /**
     * Used to display a count / total stat
     */
    public function sendstatflashmessage(int $count, int $total, string $message): void
    {
        if ($count === $total) {
            $this->sendflashmessage($count . ' / ' . $total . ' ' . $message, self::FLASH_SUCCESS);
        } elseif ($count > 0) {
            $this->sendflashmessage($count . ' / ' . $total . ' ' . $message, self::FLASH_WARNING);
        } else {
            $this->sendflashmessage($count . ' / ' . $total . ' ' . $message, self::FLASH_ERROR);
        }
    }

    /**
     * Destroy session and cookie token in user database
     */
    protected function disconnect(): void
    {
        try {
            $this->user->destroysession($this->servicesession->getwsessionid());
            $cookiemanager = new Modelconnect();
            $cookiemanager->deleteauthcookie();
            $this->servicesession->empty();
            $this->usermanager->update($this->user);
        } catch (Databaseexception $e) {
            Logger::errorex($e);
        }
    }

    /**
     * Tell if the current user can edit the given Page
     *
     * User need to be SUPEREDITOR, otherwise, it need to be author of a page.
     *
     * @param Page $page
     */
    protected function canedit(Page $page): bool
    {
        if ($this->user->issupereditor()) {
            return true;
        } elseif ($this->user->isinvite() || $this->user->iseditor()) {
            return (in_array($this->user->id(), $page->authors()));
        } else {
            return false;
        }
    }

    /**
     * Tell if the current user can delete the given Page
     *
     * User need to be SUPEREDITOR, otherwise, it need to be the only author of a page.
     *
     * @param Page $page
     */
    protected function candelete(Page $page): bool
    {
        if ($this->user->issupereditor()) {
            return true;
        } elseif ($this->user->isinvite() || $this->user->iseditor()) {
            return ($page->authors() === [$this->user->id()]);
        } else {
            return false;
        }
    }



    /**
     * Add a message to flash message list
     *
     * @param string $content The message content
     * @param string $type Message Type, can be `info|warning|success|error`
     */
    protected function sendflashmessage(string $content, string $type = self::FLASH_INFO): void
    {
        if (!key_exists($type, self::FLASH_MESSAGE_TYPES)) {
            throw new DomainException('invalid flash message type');
        }
        $_SESSION['flashmessages'][] = ['content' => $content, 'type' => $type];
    }

    /**
     * Read then empty session to get flash messages
     *
     * @return array ordered array containing array with content and type as keys or empty array
     */
    public static function getflashmessages(): array
    {
        if (!empty($_SESSION['flashmessages'])) {
            $flashmessage = $_SESSION['flashmessages'];
            $_SESSION['flashmessages'] = [];
            if (is_array($flashmessage)) {
                return $flashmessage;
            } else {
                return [];
            }
        } else {
            return [];
        }
    }
}
