<?php


session_start();



require('app/fn/fn.php');


require('./vendor/autoload.php');


$app = new Wcms\Application();
$app->wakeup();
Sentry\init(['dsn' => Wcms\Config::sentrydsn()]);

try {
    $matchoper = new Wcms\Routes();
    $matchoper->match();

} catch (Exception $e) {
    echo '<h1>⚠ Woops ! There is a little problem : </h1>', $e->getMessage(), "\n";
}






?>