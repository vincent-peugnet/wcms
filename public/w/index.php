<?php

//inw

require('../../fn/fn.php');
require('../../class/class.art.php');
require('../../class/class.app.php');
$config = include('../../config.php');
$app = new App($config);

session();

// fin de in


head('article');


// $art = new Art([
//     'id' => 'prout',
//     'titre' => 'Prout',
//     'soustitre' => 'mega prout !',
//     'intro' => 'bienvenue dans le mega prout',
//     'datemodif' => new DateTimeImmutable(null, timezone_open("Europe/Paris"))
// ]);

$arraytest = ([
    'id' => 'articlet2',
    'titre' => 'titre',
    'soustitre' => 'soustitre',
    'intro' => 'intro',
    'tag' => 'sans tag,',
    'datecreation' => '2018-03-17 18:31:34',
    'datemodif' => '2018-03-17 18:31:34',
    'css' => 'display: inline:',
    'html' => 'coucou les loulous',
    'secure' => 0,
    'couleurtext' => '#000000',
    'couleurbkg' => '#ffffff',
    'couleurlien' => '#2a3599'
]);

// $art = new Art($arreytest);


// echo '<pre>';
// print_r($art);
// print_r($app);
// echo '</pre>';

// $app->add($art);

// echo '<p>art count :' . $app->count() . '</p>';
// echo '<p>article exist :' . $app->exist('articlet') . '</p>';
// var_dump($app->exist('articlet'));



echo '<pre>';
$art = $app->get('articlet');

var_dump($art);

echo 'count : ' . $app->count();

 var_dump($app->exist('bouffffe'));
 var_dump($app->exist('bouffe'));


 $art2 = new Art($arraytest);

//  $app->add($art2);

$app->update($art2);

var_dump($app->getlist());

echo '</pre>';







?>
