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
        if (!Config::comments()) {
            http_response_code(400);
            $this->showtemplate('forbidden', ['message' => 'Comments are disabled globally']);
        }

        try {
            $page = $this->pagemanager->get($page);
        } catch (RuntimeException $e) {
            http_response_code(404);
            $this->showtemplate('forbidden', ['message' => $e->getMessage()]);
        }


        if (!isset($_POST[Modelcomment::CONFIG_POST_NAME])) {
            Logger::warning("comment on page '%s': missing config token", $page->id());
            http_response_code(400);
            exit;
        }

        try {
            $token = $_POST[Modelcomment::CONFIG_POST_NAME];
            $jwt = new JWT(Config::secretkey());
            $confdata = $jwt->decode($token);

            if (!isset($confdata['id']) || $confdata['id'] !== $page->id()) {
                Logger::warning("comment on page '%s': ID don't match", $page->id());
                http_response_code(400); // page do not match
                exit;
            }

            $conf = new Commentconf($confdata['id'], $confdata);
        } catch (JWTException | RuntimeException $e) {
            Logger::warning("comment on page '%s': config error: %s", $page->id(), $e);
            http_response_code(400);
            exit;
        }

        // check if visitors are allowed to comment
        if ($conf->mode() !== Commentconf::VISITOR_MODE && $this->user->isvisitor()) {
            http_response_code(403);
            exit;
        }

        // check if comment limit is reached
        if ($conf->limit() !== null && $page->commentcount() >= $conf->limit()) {
            http_response_code(400);
            exit;
        }

        $comment = new Comment($_POST);
        $comment->setdate(new DateTimeImmutable());

        if ($conf->mode() !== Commentconf::VISITOR_MODE) {
            $comment->setusername($this->user->id());
            $comment->setvalidated(true); // logged in user comments are validated by default
        }

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

    public function moderation(string $page): never
    {
        $pageid = $page;
        try {
            $page = $this->pagemanager->get($pageid);
        } catch (RuntimeException $e) {
            http_response_code(404);
            $this->showtemplate('forbidden');
        }

        if (!$this->canedit($page)) {
            http_response_code(401);
            $this->showtemplate('forbidden');
        }

        $validcommentids = $_POST['validatedcomment'] ?? [];

        try {
            $this->commentmanager->validateids($pageid, $validcommentids);

            // invalidate cache of the page that store the comments
            // we assume here that there's a lot of chance the page display it's own comments
            // but nothing is done for other pages that would print the comments
            // by not updating page->datecomment here, it can still be used as last comment date
            $this->pagemanager->removecache($pageid);

            $this->pagemanager->update($page);
        } catch (RuntimeException $e) {
            http_response_code(500);
            exit;
        }

        $this->routedirect('pageedit', ['page' => $pageid]);
    }
}
