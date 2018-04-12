<?php


// _____________________________________________________ R E Q U I R E ________________________________________________________________



$config = require('../../config.php');
require('../../vendor/autoload.php');
use Michelf\Markdown;
use Michelf\MarkdownExtra;


require('../../fn/fn.php');
require('../../class/class.art.php');
require('../../class/class.app.php');
require('../../class/class.aff.php');
session();
if (!isset($_SESSION['level'])) {
    $level = 0;
} else {
    $level = $_SESSION['level'];
}
$app = new App($config);
$aff = new Aff($level);


// _____________________________________________________ A C T I O N __________________________________________________________________

if (isset($_POST['action'])) {
    switch ($_POST['action']) {

        case 'update':
            if ($app->exist($_GET['id'])) {
                $art = new Art($_POST);
                $app->update($art);
                header('Location: ?id=' . $art->id() . '&edit=1');
            }
            break;

        case 'delete':
            if ($app->exist($_GET['id'])) {
                $art = new Art($_POST);
                $app->delete($art);
                header('Location: ?id=' . $art->id());
            }
            break;


        case 'login':
            $_SESSION['level'] = $app->login($_POST['pass']);
            if (isset($_GET['id'])) {
                header('Location: ?id=' . $_GET['id']);
            } else {
                header('Location: ?');
            }
            break;

        case 'logout':
            $_SESSION['level'] = $app->logout();
            if (isset($_GET['id'])) {
                header('Location: ?id=' . $_GET['id']);
            } else {
                header('Location: ?');
            }
            break;

    }

}




// _______________________________________________________ H E A D _____________________________________________________________
$titre = 'home';
if (isset($_GET['id'])) {
    $titre = $_GET['id'];
    if ($app->exist($_GET['id'])) {
        $art = $app->get($_GET['id']);
        $titre = $art->titre();
    }
}
$aff->head($titre);




// ______________________________________________________ B O D Y _______________________________________________________________ 


echo '<body>';
$aff->nav($app);

if (isset($_GET['id'])) {


    if ($app->exist($_GET['id'])) {

        $art = $app->get($_GET['id']);

        if (isset($_GET['edit']) and $_GET['edit'] == 1) {
            $aff->edit($art);
            $aff->aside($app->lister());
        } else {
            $aff->lecture($art);

        }
    } else {
        if (isset($_POST['action'])) {
            if ($_POST['action'] == 'new') {
                $art = new Art($_GET);
                $art->reset();
                $app->add($art);
                header('Location: ?id=' . $_GET['id'] . '&edit=1');
            }
        } else {
            echo '<span class="alert"><h4>Cet article n\'existe pas encore</h4></span>';

            if ($level >= 2) {
                echo '<form action="?id=' . $_GET['id'] . '&edit=1" method="post"><input type="hidden" name="action" value="new"><input type="submit" value="créer"></form>';
            }

        }

    }
} elseif (isset($_GET['tag'])) {
    echo '<h4>' . $_GET['tag'] . '</h4>';
    $aff->tag($app->getlister(), $_GET['tag']);

} else {
    $aff->home($app->lister());
}
echo '</body>';



?>
