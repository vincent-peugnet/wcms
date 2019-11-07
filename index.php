<?php


session_start();



require('app/fn/fn.php');


require('./vendor/autoload.php');


$app = new Wcms\Application();
$app->wakeup();
Sentry\init([
    'dsn' => Wcms\Config::sentrydsn(),
    'release' => 'w_cms_v' . getversion(),
    'project_root' => 'app',
]);
Sentry\configureScope(function (Sentry\State\Scope $scope): void {
    $scope->setUser([
        'id' => Wcms\Config::url(),
        'username' => Wcms\Config::basepath(),
    ]);
});

try {
    $matchoper = new Wcms\Routes();
    $matchoper->match();

} catch (Exception $e) {
    Sentry\captureException($e);
    echo '<h1>⚠ Woops ! There is a little problem : </h1>', $e->getMessage(), "\n";
}






?>