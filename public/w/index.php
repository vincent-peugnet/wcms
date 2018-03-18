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

// $arraytest = ([
//     'id' => 'articlet2',
//     'titre' => 'titre',
//     'soustitre' => 'soustitre',
//     'intro' => 'intro',
//     'tag' => 'sans tag,',
//     'datecreation' => '2018-03-17 18:31:34',
//     'datemodif' => '2018-03-17 18:31:34',
//     'css' => 'display: inline:',
//     'html' => 'coucou les loulous',
//     'secure' => 0,
//     'couleurtext' => '#000000',
//     'couleurbkg' => '#ffffff',
//     'couleurlien' => '#2a3599'
// ]);

// $art = new Art($arreytest);


// echo '<pre>';
// print_r($art);
// print_r($app);
// echo '</pre>';

// $app->add($art);

// echo '<p>art count :' . $app->count() . '</p>';
// echo '<p>article exist :' . $app->exist('articlet') . '</p>';
// var_dump($app->exist('articlet'));

$session = 2;


if (isset($_GET['id'])) {
    
    if ($session == 2) {
        ?>
        <nav>
        <a href="?" >home</a>
        <a href="?id=<?= $_GET['id'] ?>&display=1" target="_blank">display</a>
        <a href="?id=<?= $_GET['id'] ?>&edit=1" >edit</a>
        </nav>
        <?php


}

if ($app->exist($_GET['id'])) {
    
    if (isset($_POST['action']) and $_POST['action'] == 'update') {
        $art = new Art($_POST);
        var_dump($art);
        $app->update($art);
        header('Location: ?id=' . $art->id() . '&edit=1');
        
    }
    
    $art = $app->get($_GET['id']);
    
    
    
    if (isset($_GET['display']) and $_GET['display'] == 1) {
        $art->display($session);
        }
        if (isset($_GET['edit']) and $_GET['edit'] == 1) {
            $art->edit($session);
        }
    } else {
        if (isset($_POST['action'])) {
            if ($_POST['action'] == 'new') {
                $art = new Art($_GET);
                $art->default();
                var_dump($art);
                $app->add($art);
                header('Location: ?id=' . $_GET['id'] . '&edit=1');
            }
        } else {
            echo '<h4>Cet article n\'éxiste pas encore</h4>';

            if ($session >= 2) {
                echo '<form action="?id=' . $_GET['id'] . '&edit=1" method="post"><input type="hidden" name="action" value="new"><input type="submit" value="créer"></form>';
            }
            echo '<a href="?info=erreur">retour maison</a>';

        }

    }
} else {
    echo "<h4>Bienvenue sur ce site.</h4>";
    $app->menu($session);
}



?>
