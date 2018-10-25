<?php

$data = ['id' => 'cool', 'redirect' => 'edit'];

require('../w/class/route.php');

$route = new Route($data);

var_dump($route);

var_dump($route->toarray());
var_dump($route->tostring());

$array = [];

var_dump(implode(' ', $array));


?>