<?php

//inw

$root = $_SERVER["DOCUMENT_ROOT"];
require($root . '/fn.php');
require($root . '/class/art.php');
$config = include($root . '/config.php');
$domaine = $config['domaine'];

session();

// fin de in

$app = new App($config);
$art = new Art;
$art->createfrombdd($id, $app->getBdd());

include($root . '/head.php')

?>