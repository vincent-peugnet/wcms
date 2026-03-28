<?php

namespace Wcms;

use Ahc\Jwt\JWT;
use Ahc\Jwt\JWTException;
use AltoRouter;
use DateTimeImmutable;
use RuntimeException;
use Wcms\Exception\Databaseexception;

class Controllercomment extends Controller
{
    protected Modelcomment $commentmanager;

    public function __construct(AltoRouter $router)
    {
        parent::__construct($router);
        $this->commentmanager = new Modelcomment();
    }

    public function comment(string $page): never
    {
        if ($this->user->isvisitor()) {
            http_response_code(403);
            exit;
        }

        try {
            $page = $this->pagemanager->get($page);
        } catch (RuntimeException $e) {
            http_response_code(404);
            $this->showtemplate('forbidden');
        }


        if (!isset($_POST[Modelcomment::CONFIG_POST_NAME])) {
            Logger::warning("comment on page '%s': missing config token", $page->id());
            http_response_code(400);
            exit;
        }

        try {
            $token = $_POST[Modelcomment::CONFIG_POST_NAME];
            $jwt = new JWT(Config::secretkey());
            $config = $jwt->decode($token);
            $conf = new Commentconf($config);
        } catch (JWTException | RuntimeException $e) {
            Logger::warning("comment on page '%s': config error: %s", $page->id(), $e);
            http_response_code(400);
            exit;
        }

        if ($conf->id !== $page->id()) {
            Logger::warning("comment on page '%s': ID don't match", $page->id());
            http_response_code(400); // page do not match
            exit;
        }

        $comment = new Comment($_POST);
        $comment->setdate(new DateTimeImmutable());
        $comment->setusername($this->user->id());

        if (!$comment->validate($conf)) {
            Logger::warning("'%s' sent a invalid comment on page '%s'", $this->user->id(), $page->id());
            http_response_code(400);
            exit;
        }


        try {
            $commentcount = $this->commentmanager->addcomment($page, $comment);
            $page->setcommentcount($commentcount);
            $page->setdatecomment($comment->date());
            $this->pagemanager->update($page);
            Logger::info('new comment on page "%s"', $page->id());
        } catch (Databaseexception $e) {
            Logger::errorex($e);
        }

        $this->routedirect('pageread', ['page' => $page->id()]);
    }
}
