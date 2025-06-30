<?php

require('./vendor/autoload.php');
mb_internal_encoding('UTF-8');

use Wcms\Model;

try {
    Wcms\Logger::init(Model::ERROR_LOG, 3);
} catch (RuntimeException $e) {
    die('Unable to init logs: ' . $e->getMessage());
}

$app = new Wcms\Application();
$app->wakeup();

session_set_cookie_params([
    'path' => '/' . Wcms\Config::basepath(),
    'samesite' => 'Strict',
    'secure' => Wcms\Config::issecure()
]);
session_start();

if (class_exists('Whoops\Run') && !empty(Wcms\Config::debug())) {
    $whoops = new \Whoops\Run();
    $handler = new \Whoops\Handler\PrettyPageHandler();
    $handler->setEditor(\Wcms\Config::debug());
    $whoops->pushHandler($handler);
    $whoops->register();
}

if (isreportingerrors()) {
    Sentry\init([
        'dsn' => Wcms\Config::sentrydsn(),
        'release' => getversion(),
    ]);
    Sentry\configureScope(function ($scope) {
        $scope->setUser([
            'id' => Wcms\Config::url(),
            'username' => Wcms\Config::basepath(),
        ]);
    });
}

try {
    $matchoper = new Wcms\Routes();
    $matchoper->match();
} catch (Throwable $e) {
    if (isreportingerrors()) {
        Sentry\captureException($e);
    }
    Wcms\Logger::errorex($e, true);
    http_response_code(500);
    if (isset($whoops)) {
        $whoops->handleException($e);
    }
    echo '<h1>⚠ Whoops ! There is a little problem : </h1>';
    echo `<p>` . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p>Please contact yout Wiki admin to solve this.</p>';
}
Wcms\Logger::close();
