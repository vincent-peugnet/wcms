<?php

session_start();


require('./vendor/autoload.php');

try {
    Wcms\Logger::init('w_error.log', 2);
} catch (Throwable $e) {
    die('Unable to init logs: ' . $e->getMessage());
}

$app = new Wcms\Application();
$app->wakeup();

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
        'project_root' => 'app',
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
    echo '<h1>⚠ Whoops ! There is a little problem : </h1>', $e->getMessage(), "\n";
}
