<?php

use Wcms\Logger;

session_start();


require('./vendor/autoload.php');

try {
    Logger::init('w_error.log', 2);
} catch (Throwable $e) {
    die('Unable to init logs: ' . $e->getMessage());
}

$app = new Wcms\Application();
$app->wakeup();

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
    Logger::errorex($e, true);
    echo '<h1>⚠ Woops ! There is a little problem : </h1>', $e->getMessage(), "\n";
}
