<?php

//inw

set_include_path('e:/WEB/wcms');

require('fn/fn.php');
require('class/class.art.php');
require('class/class.app.php');
$config = include('config.php');
$app = new App();

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

// echo '<pre>';
// print_r($art);
// print_r($app);
// echo '</pre>';

// $app->add($art);

// echo '<p>art count :' . $app->count() . '</p>';
// echo '<p>article exist :' . $app->exist('articlet') . '</p>';
// var_dump($app->exist('articlet'));

// $app->get('articlet');

// echo '<pre>';
// print_r($art);
// print_r($app);
// echo '</pre>';

try {
    $bdd = new PDO('mysql:host=localhost;dbname=wcms;charset=utf8', 'root', '');
} catch (Exeption $e) {
    die('Erreur : ' . $e->getMessage());
}

$q = $bdd->query('SELECT * FROM art WHERE id = articlet');
$donnees = $q->fetch();

var_dump($donnees);

$q = closeCursor();



?>