<?php

//inw

$root = $_SERVER["DOCUMENT_ROOT"];
require($root . '/fn.php');
require($root . '/class/art.php');
$config = include($root . '/config.php');
$domaine = $config['domaine'];

session();

// fin de in

$art = new Art;
$art->createfrombdd($id, bddconnect($config['host'], $config['dbname'], $config['user'], $config['password']));

include($root . '/head.php')

?>