<?php

session_start();


require('./vendor/autoload.php');


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
} catch (Exception $e) {
    if (isreportingerrors()) {
        Sentry\captureException($e);
    }
    echo '<h1>⚠ Woops ! There is a little problem : </h1>', $e->getMessage(), "\n";
}
