<?php


require('../w/class/art2.php');
require('../w/class/render.php');

$render = new Render(['head' => 'ccccccc', 'body' => 'vvvvvvvvvvvv']);

$render2 = ['head' => 'nnnnnnnnnn', 'body' => 'bbbbbbbbbbbbbbbbbbbbbbb'];

$render3 = json_decode(json_encode($render2));

var_dump($render3);

var_dump($render);

$art = new Art2(['id' => 'rr']);
$art->reset();
$art->hydrate((['description' => 'fdsfs', 'secure' => 0, 'render' => $render2]));







?>