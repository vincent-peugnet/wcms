<?php


// _____________________________________________________ R E Q U I R E ________________________________________________________________

require('../../vendor/autoload.php');
use Michelf\Markdown;
use Michelf\MarkdownExtra;

require('../../fn/fn.php');
require('../../class/class.w.config.php');
require('../../class/class.w.art.php');
require('../../class/class.w.app.php');
require('../../class/class.w.aff.php');
require('../../class/class.w.media.php');
require('../../class/class.w.record.php');

// ________________________________________________________ I N S T A L _________________________________________________

$app = new App();
$aff = new Aff();


$config = $app->readconfig();
if (!$config) {
    $message = 'config_file_error';
    echo $message;
    if (isset($_POST['config']) && $_POST['config'] == 'create') {
        $config = $app->createconfig($_POST);
        $app->savejson($config->tojson());
        header('Location: ./');

    } else {
        $aff->configform();
    }
    exit;
}


// _________________________________________________________ S E S ___________________________________________________________

session();
if (!isset($_SESSION['level'])) {
    $session = 0;
} else {
    $session = $_SESSION['level'];
}

$app->setsession($session);

//var_dump($config);
//var_dump($app);



// _________________________________________________________ N A V _______________________________________________

if (isset($_GET['id'])) {
    $app->setbdd($config);
}


// _____________________________________________________ A C T I O N __________________________________________________________________


if (isset($_POST['action'])) {
    switch ($_POST['action']) {

        case 'login':
            $_SESSION['level'] = $app->login($_POST['pass'], $config);
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
            $message = $app->addmedia($_FILES, 2 ** 24, $_POST['id']);
            header('Location: ./?aff=media&message=' . $message);
            break;

        case 'addcss':
            $message = $app->addcss($_FILES, 2 ** 24, $_POST['id']);
            header('Location: ./?aff=admin&message=' . $message);
            break;

        case 'changecss':
            $config->setcssread($_POST['lecturecss']);
            $app->savejson($config->tojson());
            header('Location: ./?aff=admin');
            break;

        case 'editconfig':
            $config->hydrate($_POST);
            $app->savejson($config->tojson());
            header('Location: ./?aff=admin');

            break;


    }
}


// _____________________________________________________ D A T A B A S E __________________________________________________________________

if (isset($_POST['action'])) {
    $app->setbdd($config);

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

    }

}


// _______________________________________________________ H E A D _____________________________________________________________

if (isset($_GET['id'])) {
    $app->setbdd($config);
    if ($app->exist($_GET['id'])) {
        $art = $app->get($_GET['id']);
        if (isset($_GET['edit']) && $_GET['edit'] == 1) {
            $aff->arthead($art, $config->cssread(), 1);
        } else {
            $aff->arthead($art, $config->cssread(), 0);
        }
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
    $app->setbdd($config);


    if ($app->exist($_GET['id'])) {

        $art = $app->get($_GET['id']);

        if (isset($_GET['edit']) and $_GET['edit'] == 1 and $app->session() >= $app::EDITOR) {
            $aff->edit($art, $app, $app->getlister(['id', 'titre'], 'id'));
            $aff->copy($art, $app->getlister(['id', 'titre'], 'id'));
            $aff->aside($app);
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

            if ($aff->session() >= 2) {
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
} elseif (isset($_GET['aff']) && $app->session() == $app::ADMIN) {
    if ($_GET['aff'] == 'admin') {
        echo '<section>';
        echo '<h1>Admin</h1>';

        $aff->admincss($config, $app->csslist());
        $aff->admindb($config);

        echo '</section>';
    } elseif ($_GET['aff'] == 'media') {
        echo '<h1>Media</h1>';
        echo '<section>';

        $aff->addmedia();
        $aff->medialist($app);

        echo '</section>';

    } elseif ($_GET['aff'] == 'record') {
        echo '<h1>Record</h1>';
        echo '<section>';

        $aff->recordlist($app);

        echo '</section>';

    } else {
        header('Location: ./');
    }

} else {
    $app->setbdd($config);

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
    $aff->home2table($app, $app->getlister(['id', 'titre', 'intro', 'lien', 'datecreation', 'datemodif'], $tri, $desc));

}

echo '</body>';


?>
