<?php


session_start();



require(__DIR__ . '/fn/w.fn.php');

function class_autoloader($class)
{
    require(__DIR__ . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . strtolower($class) . '.php');
}


require(__DIR__ . '/../vendor/autoload.php');

spl_autoload_register('class_autoloader');


$app = new Application();
$app->wakeup();

try {
    $matchoper = new Routes();
    $matchoper->match();

} catch (Exception $e) {
    echo 'Exception reçue : ', $e->getMessage(), "\n";
}






?>