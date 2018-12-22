<?php


session_start();



require('app/fn/fn.php');


require('./vendor/autoload.php');

spl_autoload_register('class_autoloader');


$app = new Application();
$app->wakeup();

try {
    $matchoper = new Routes();
    $matchoper->match();

} catch (Exception $e) {
    echo '⚠ ⚠ ⚠ Sorry, there is a little problem : ', '<i>'.$e->getMessage().'</i>', "\n";
}






?>