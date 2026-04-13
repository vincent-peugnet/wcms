<?php

namespace Wcms;

use Ahc\Jwt\JWT;
use Ahc\Jwt\JWTException;
use AltoRouter;
use DateInterval;
use DomainException;
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
            $this->showtemplate('alertcomment', ['message' => 'Comments are disabled globally'], 400);
        }

        try {
            $page = $this->pagemanager->get($page);
        } catch (RuntimeException $e) {
            $this->showtemplate('alertcomment', ['message' => $e->getMessage()], 404);
        }

        // users who cannot read the page cannot post comments
        if (!$this->canread($page)) {
            $this->showtemplate('alertcomment', ['message' => 'unauthorized'], 401);
        }


        if (!isset($_POST[Modelcomment::CONFIG_POST_NAME])) {
            $msg = sprintf("comment on page '%s': missing config token", $page->id());
            Logger::warning($msg);
            $this->showtemplate('alertcomment', ['message' => $msg], 400);
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
            $msg = sprintf("comment on page '%s': comment config decoding error", $page->id());
            Logger::warning($msg);
            $this->showtemplate('alertcomment', ['message' => $msg], 400);
        }

        // check if is JWT is not outdated
        if ($conf->datemodif() != $page->datemodif()) {
            $yesterday = $this->now->sub(new DateInterval('PT24H'));
            if ($page->datemodif() < $yesterday) { // page has been edited since more than 24h
                $this->showtemplate('alertcomment', ['message' => 'outdated comment configuration'], 400);
            }
        }

        // check if visitors are allowed to comment
        if (!$conf->allowvisitor() && $this->user->isvisitor()) {
            http_response_code(401);
            $this->showtemplate('alertcomment', ['message' => 'comment not allowed from visitors']);
        }

        // check if comment limit is reached
        if ($conf->limit() !== null && $page->commentcount() >= $conf->limit()) {
            $msg = sprintf("comment limit is reached on page '%s'", $page->id());
            Logger::warning($msg);
            $this->showtemplate('alertcomment', ['message' => $msg], 400);
        }

        switch ($conf->mode()) {
            case Commentconf::VISITOR_MODE:
                $comment = new Commentvisitor($_POST);
                break;

            case Commentconf::USER_MODE:
                $comment = new Commentuser($_POST);
                $comment->setuser($this->user->id());
                $comment->setapproved(true); // logged in user comments are approved by default
                break;

            case Commentconf::ALL_MODE:
                if ($this->user->isvisitor()) {
                    $comment = new Commentvisitor($_POST);
                } else {
                    $comment = new Commentuser($_POST);
                    $comment->setuser($this->user->id());
                    $comment->setapproved(true); // logged in user comments are approved by default
                }
                break;

            default:
                throw new DomainException('Commentconf object mode is set to unauthorized value');
        }

        $comment->setdate($this->now);

        if (!$comment->validate($conf)) {
            Logger::warning("'%s' sent a invalid comment on page '%s'", $this->user->id(), $page->id());
            $this->showtemplate('alertcomment', ['message' => 'invalid comment'], 400);
        }


        try {
            $commentcount = $this->commentmanager->addcomment($page, $comment);
            $page->setcommentcount($commentcount);
            $page->setdatecomment($comment->date());
            $this->pagemanager->update($page);
            Logger::info('new comment on page \'%s\'', $page->id());

            if (!empty($conf->success())) {
                $this->servicesession->addalert($page->id(), $conf->success());
            }
        } catch (Databaseexception $e) {
            Logger::errorex($e);
            $this->showtemplate('alertcomment', ['message' => 'database error'], 500);
        }

        $this->routedirect('pageread', ['page' => $page->id()]);
    }

    public function moderation(string $page): never
    {
        $pageid = $page;
        try {
            $page = $this->pagemanager->get($pageid);
        } catch (RuntimeException $e) {
            $this->showtemplate(
                'alertexistnot',
                ['page' => new Pagev2(['id' => $pageid]), 'subtitle' => Config::existnot()],
                404
            );
        }

        if (!$this->canedit($page)) {
            $this->showtemplate('forbidden', [], 401);
        }

        try {
            $this->commentmanager->applymoderation($pageid, $_POST);

            // invalidate cache of the page that store the comments
            // we assume here that there's a lot of chance the page display it's own comments
            // but nothing is done for other pages that would print the comments
            // by not updating page->datecomment here, it can still be used as last comment date
            $this->pagemanager->removecache($pageid);

            $this->pagemanager->update($page);
        } catch (RuntimeException $e) {
            http_response_code(500);
            Logger::error('comment moderation: %s', $e->getMessage());
            exit;
        }

        $this->routedirect('pageedit', ['page' => $pageid]);
    }
}
