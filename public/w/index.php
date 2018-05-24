<?php


// _____________________________________________________ R E Q U I R E ________________________________________________________________



$config = require('../../config.php');
require('../../vendor/autoload.php');
use Michelf\Markdown;
use Michelf\MarkdownExtra;


require('../../fn/fn.php');
require('../../class/class.w.art.php');
require('../../class/class.w.app.php');
require('../../class/class.w.aff.php');
require('../../class/class.w.media.php');
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

        case 'copy':
            if ($app->exist($_GET['id'])) {
                $copy = $app->get($_POST['copy']);
                $art = $app->get($_POST['id']);
                if (!empty($_POST['css'])) {
                    $art->setcss($copy->css());
                }
                if (!empty($_POST['color'])) {
                    $art->setcouleurtext($copy->couleurtext());
                    $art->setcouleurbkg($copy->couleurbkg());
                    $art->setcouleurlien($copy->couleurlien());
                    $art->setcouleurlienblank($copy->couleurlienblank());
                }
                if (!empty($_POST['html'])) {
                    $art->sethtml($copy->md());
                }
                if (!empty($_POST['template'])) {
                    $art->settemplate($copy->template());
                }
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
                header('Location: ./');
            }
            break;

        case 'logout':
            $_SESSION['level'] = $app->logout();
            if (isset($_GET['id'])) {
                header('Location: ?id=' . $_GET['id']);
            } else {
                header('Location: ./');
            }
            break;

        case 'addmedia':
            $message = $app->addmedia($_FILES, 2 ** 10, $_POST['id']);
            header('Location: ./?aff=media&message='.$message);
            break;

    }

}




// _______________________________________________________ H E A D _____________________________________________________________

if (isset($_GET['id'])) {
    if ($app->exist($_GET['id'])) {
        $art = $app->get($_GET['id']);
        if (isset($_GET['edit']) && $_GET['edit'] == 1) {
            $aff->arthead($art, '✏');
        }
        $aff->arthead($art, '');
    } else {
        $aff->head($_GET['id'], 'w');

    }
} elseif (isset($_GET['aff'])) {
    $aff->head($_GET['aff'], $_GET['aff']);
} else {
    $aff->head('home', 'w');
}







// _____________________________________________________ A L E R T _______________________________________________________________ 

if (isset($_GET['message'])) {
    echo '<span class="alert"><h4>' . $_GET['message'] . '</h4></span>';
}








// ______________________________________________________ B O D Y _______________________________________________________________ 

echo '<body>';
$aff->nav($app);

if (isset($_GET['id'])) {

    if ($app->exist($_GET['id'])) {

        $art = $app->get($_GET['id']);

        if (isset($_GET['edit']) and $_GET['edit'] == 1 and $aff->session() == 2) {
            $aff->edit($art, $app->getlister(['id', 'titre'], 'id'));
            $aff->copy($art, $app->getlister(['id', 'titre'], 'id'));
            $aff->aside($app->lister());
        } else {
            $aff->lecture($art, $app);

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
    $aff->tag($app->getlister(['id', 'titre', 'intro', 'tag'], 'id'), $_GET['tag']);

} elseif (isset($_GET['lien'])) {
    echo '<h4>' . $_GET['lien'] . '</h4>';
    $aff->lien($app->getlister(['id', 'titre', 'intro', 'lien'], 'id'), $_GET['lien']);

} elseif (isset($_GET['aff'])) {
    if ($_GET['aff'] == 'admin') {
        echo '<h1>Admin</h1>';
    } elseif ($_GET['aff'] == 'media') {
        echo '<section>';

        $aff->addmedia();
        $aff->medialist($app);

        echo '</section>';

    } elseif ($_GET['aff'] == 'record') {
        echo '<h1>Record</h1>';
    } else {
        header('Location: ./');
    }

} else {
    if (isset($_GET['tri'])) {
        $tri = strip_tags($_GET['tri']);
    } else {
        $tri = 'id';
    }
    if (isset($_GET['desc'])) {
        $desc = strip_tags($_GET['desc']);
    } else {
        $desc = 'ASC';
    }
    $aff->home2table($app->getlister(['id', 'titre', 'intro', 'lien', 'datecreation', 'datemodif'], $tri, $desc));

}

echo '</body>';


?>
