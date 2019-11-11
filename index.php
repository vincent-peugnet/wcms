<?php


session_start();



require('app/fn/fn.php');


require('./vendor/autoload.php');


$app = new Wcms\Application();
$app->wakeup();
try {
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
} catch (Throwable $th) {
    // No problem: Sentry is optionnal
}

try {
    $matchoper = new Wcms\Routes();
    $matchoper->match();

} catch (Exception $e) {
    try {
        Sentry\captureException($e);
    } catch (Throwable $th) {
        // No problem: Sentry is optionnal
    }
    echo '<h1>⚠ Woops ! There is a little problem : </h1>', $e->getMessage(), "\n";
}






?>