<?php

namespace Wcms;

use RuntimeException;
use Throwable;
use Wcms\Exception\Filesystemexception\Notfoundexception;

require('./vendor/autoload.php');
mb_internal_encoding('UTF-8');
umask(0);


try {
    Logger::init(Model::ERROR_LOG, 3);
} catch (RuntimeException $e) {
    die('Unable to init logs: ' . $e->getMessage());
}


try {
    try {
        Config::readconfig();
    } catch (Notfoundexception $e) {
        $wizard = new Wizard();
        $wizard->launch();
        exit;
    } catch (RuntimeException $e) {
        throw new RuntimeException('config error: ' . $e->getMessage());
    }

    date_default_timezone_set(Config::timezone());

    session_set_cookie_params([
        'path' => '/' . Config::basepath(),
        'samesite' => 'Strict',
        'secure' => Config::issecure()
    ]);
    session_start();

    if (class_exists('Whoops\Run') && !empty(Config::debug())) {
        $whoops = new \Whoops\Run();
        $handler = new \Whoops\Handler\PrettyPageHandler();
        $handler->setEditor(Config::debug());
        $whoops->pushHandler($handler);
        $whoops->register();
    }

    if (isreportingerrors()) {
        \Sentry\init([
            'dsn' => Config::sentrydsn(),
            'release' => getversion(),
        ]);
        \Sentry\configureScope(function ($scope) {
            $scope->setUser([
                'id' => Config::url(),
                'username' => Config::basepath(),
            ]);
        });
    }

    $matchoper = new Routes();
    $matchoper->match();
} catch (Throwable $e) {
    if (isreportingerrors()) {
        \Sentry\captureException($e);
    }
    Logger::errorex($e, true);
    http_response_code(500);
    if (isset($whoops)) {
        $whoops->handleException($e);
    }
    echo '<h1>⚠ Whoops ! There is a little problem : </h1>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p>Please contact yout Wiki admin to solve this.</p>';
}
Logger::close();
