<?php

// _____________________________________________________ R E Q U I R E ________________________________________________________________

session_start();

require('../../vendor/autoload.php');
require('../../fn/fn.php');

spl_autoload_register('my_autoloader');




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


if (!isset($_SESSION['level'])) {
    $session = 0;
} else {
    $session = $_SESSION['level'];
}

$app->setsession($session);




// _______________________________________________________ A C T I O N __________________________________________________________________


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

        case 'editconfig':
            $config->hydrate($_POST);
            $app->savejson($config->tojson());
            header('Location: ./?aff=admin');

            break;


    }
}



// _____________________________________________________ D A T A B A S E __________________________________________________________________

if (isset($_POST['action'])) {
    $app->bddinit($config);

    switch ($_POST['action']) {

        case 'new':
            if (isset($_GET['id'])) {
                $art = new Art($_GET);
                $art->reset();
                $app->add($art);
                header('Location: ?id=' . $_GET['id'] . '&edit=1');
            }
            break;

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

        case 'massedit':
            if (isset($_POST['id'])) {
                foreach ($_POST['id'] as $id) {
                    $art = new Art(['id' => $id]);
                    if ($_POST['massedit'] == 'delete' && $app->exist($id)) {
                        $app->delete($art);
                    }
                    if ($_POST['massedit'] >= 0 && $app->exist($id)) {
                        $art = $app->get($id);
                        $art->setsecure($_POST['massedit']);
                        $app->update($art);
                    }
                    header('Location: ./');
                }

            }
            break;

    }

}

if (isset($_POST['actiondb'])) {
    $app->setbdd($config);

    switch ($_POST['actiondb']) {

        case 'addtable':
            if (isset($_POST['tablename'])) {
                $message = $app->addtable($config->dbname(), $_POST['tablename']);
                header('Location: ./?aff=admin&message=' . $message);
            }
            break;

    }
}




// _______________________________________________________ H E A D _____________________________________________________________

if (isset($_GET['id'])) {
    $app->bddinit($config);
    if ($app->exist($_GET['id'])) {
        $art = $app->get($_GET['id']);
        if (!isset($_GET['edit'])) {
            $_GET['edit'] = 0;
        }
        $aff->arthead($art, $config->cssread(), $_GET['edit']);
    } else {
        $aff->head($_GET['id'], '');

    }
} elseif (isset($_GET['aff'])) {
    $aff->head($_GET['aff'], $_GET['aff']);
} else {
    $aff->head('home', 'home');
}







// _____________________________________________________ A L E R T _______________________________________________________________ 

if (isset($_GET['message'])) {
    echo '<span class="alert">' . $_GET['message'] . '</span>';
}








// ______________________________________________________ B O D Y _______________________________________________________________ 


$aff->nav($app);



if (array_key_exists('id', $_GET)) {
    $app->bddinit($config);
    include('article.php');
} elseif (array_key_exists('tag', $_GET)) {
    $app->bddinit($config);
    echo '<h4>' . $_GET['tag'] . '</h4>';
    $aff->tag($app->getlister(['id', 'titre', 'intro', 'tag'], 'id'), $_GET['tag'], $app);
} elseif (array_key_exists('lien', $_GET)) {
    $app->bddinit($config);
    echo '<h4><a href="?id=' . $_GET['lien'] . '">' . $_GET['lien'] . '</a></h4>';
    $aff->lien($app->getlister(['id', 'titre', 'intro', 'lien']), $_GET['lien'], $app);
} elseif (array_key_exists('aff', $_GET)) {
    include('menu.php');
} else {
    include('home.php');
}



?>