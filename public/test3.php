<?php

$path = '../w/class/';
$lengh = strlen($path);
$array = [];
foreach (glob($path . '*.php') as $filename) {
	$array[] = substr(substr($filename, $lengh), 0, -4);
}

var_dump($array);









?>