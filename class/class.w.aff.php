<?php

class Aff
{

    private $session;

    private static $edit = 2;


    // ____________________________________________________ F U N ______________________________________________

    public function __construct($session)
    {
        $this->setsession($session);
    }

    public function lecture(Art $art, App $app)
    {
        echo '<div class="lecture">';
        if ($art->secure() == 1) {
            echo '<span class="alert"><h4>cet article est privé</h4></span>';
        }
        if ($art->secure() == 2) {
            echo "<span class=\"alert\"><h4>cet article n'est pas publié</h4></span>";
        }

        if ($this->session() >= $art->secure()) {
            ?>
            <style type="text/css">
            <?= $art->csstemplate($app) ?>
            body{
                background: <?= $art->couleurbkg() ?>;
            }
            section {
                color: <?= $art->couleurtext() ?>;			
            }
            
            a {
                color: <?= $art->couleurlien() ?>;
            }

            section a[target="_blank"] {
                color: <?= $art->couleurlienblank() ?>;
            }
            </style>
            <section>
            <header>
            <h1><?= $art->titre() ?></h1>
            <h6><?= $art->soustitre() ?></h6>
            </header>
            <article><?= $art->html($app) ?></article>
            </section>
            <?php

        }
        echo '</div>';
    }

    public function edit(Art $art, $list)
    {
        if ($this->session() >= self::$edit) {

            ?>
    <div class="edit">                
        <section>
            <form action="?id=<?= $art->id() ?>" method="post">
                <details close>
                    <summary>Infos</summary>
                    <label for="titre">Titre :</label>
                    <input type="text" name="titre" id="titre" value="<?= $art->titre(); ?>">
                    <label for="soustitre">Sous-titre :</label>
                    <input type="text" name="soustitre" id="soustitre" value="<?= $art->soustitre(); ?>">
                    <label for="intro">Introduction :</label>
                    <input type="text" name="intro" id="intro" value="<?= $art->intro(); ?>">
                    <label for="tag">Tag(s) :</label>
                    <input type="text" name="tag" id="tag" value="<?= $art->tag('string'); ?>">
                    <label for="secure">Niveau de sécurité :</label>
                    <select name="secure" id="secure">
                        <option value="0" <?= $art->secure() == 0 ? 'selected' : '' ?>>0</option>
                        <option value="1" <?= $art->secure() == 1 ? 'selected' : '' ?>>1</option>
                        <option value="2" <?= $art->secure() == 2 ? 'selected' : '' ?>>2</option>
                    </select>
                </details>
                <details close>
                    <summary>CSS</summary>
                    <label for="template">Template :</label>
                    <select name="template" id="template">
                    <?php
                    if ($art->template() == 'NULL') {
                        echo '<option value="" selected >Sans template</option>';
                    } else {
                        echo '<option value="" >sans template</option>';
                    }
                    foreach ($list as $item) {

                        if ($item->id() == $art->template()) {
                            echo '<option value="' . $item->id() . '" selected >' . $item->titre() . '</option>';
                        } else {
                            echo '<option value="' . $item->id() . '">' . $item->titre() . '</option>';
                        }
                    }
                    ?>
                    </select>
                    <label for="css">Styles CSS :</label>
                    <textarea name="css" id="css"><?= $art->css(); ?></textarea>
                    <label for="couleurtext">Couleur du texte :</label>
                    <input type="color" name="couleurtext" value="<?= $art->couleurtext() ?>" id="couleurtext">
                    <label for="couleurbkg">Couleur de l'arrière plan :</label>
                    <input type="color" name="couleurbkg" value="<?= $art->couleurbkg() ?>" id="couleurbkg">
                    <label for="couleurlien">Couleur des liens :</label>
                    <input type="color" name="couleurlien" value="<?= $art->couleurlien() ?>" id="couleurlien">
                    <label for="couleurlienblank">Couleur des liens externes :</label>
                    <input type="color" name="couleurlienblank" value="<?= $art->couleurlienblank() ?>" id="couleurlienblank">
                </details>
                <details open>
                    <summary>Contenu</summary>
                    <textarea name="html" id="html" ><?= $art->md(); ?></textarea>
                </details>
                <input type="hidden" name="datecreation" value="<?= $art->datecreation('string'); ?>">
                <input type="hidden" name="id" value="<?= $art->id() ?>">
            </section>
                <div class="submit">
                    <input type="submit" name="action" value="update">
                    <input type="submit" name="action" value="delete" onclick="confirmSubmit(event, 'Suppression de cet article')">
        </form>
                </div>
        </div>

    <?php

}

}

public function copy(Art $art, $list)
{
    ?>
    <div class="copy">
    <form action="?id=<?= $art->id() ?>&edit=1" method="post">
    <input type="hidden" name="action" value="copy">
    <input type="hidden" name="id" value="<?= $art->id() ?>">
    <select name="copy">
        <?php
        foreach ($list as $item) {
            echo '<option value="' . $item->id() . '">' . $item->titre() . '</option>';
        }
        echo '</select>';
        ?>
    <label for="checkcss">CSS</label>
    <input type="checkbox" id="checkcss" name="css" value="true">
    <label for="checkcolor">Color</label>
    <input type="checkbox" id="checkcolor" name="color" value="true">
    <label for="checkhtml">HTML</label>
    <input type="checkbox" id="checkhtml" name="html" value="true">
    <label for="checktemplate">template</label>
    <input type="checkbox" id="checktemplate" name="template" value="true">
    <input type="submit" value="copy" onclick="confirmSubmit(event, 'Ecraser ces valeurs')">
    </form>
    </div>
    <?php

    }

    public function head($title, $tool)
    {
    ?>
    <head>
        <meta charset="utf8" />
        <meta name="viewport" content="width=device-width" />
        <link rel="shortcut icon" href="../media/logo.png" type="image/x-icon">
        <link href="/css/stylebase.css" rel="stylesheet" />
        <link href="/css/style<?= $tool ?>.css" rel="stylesheet" />
        <title><?= $title ?></title>
        <script src="../rsc/js/app.js"></script>
    </head>
    <?php

    }

    public function arthead(Art $art, $pre = '')
    {
    ?>
    <head>
        <meta charset="utf8" />
        <meta name="description" content="<?= $art->intro() ?>" />
        <meta name="viewport" content="width=device-width" />
        <link rel="shortcut icon" href="../media/logo.png" type="image/x-icon">
        <link href="/css/stylebase.css" rel="stylesheet" />
        <link href="/css/stylew.css" rel="stylesheet" />
        <title><?= $pre ?> <?= $art->titre() ?></title>
        <script src="../rsc/js/app.js"></script>
    </head>
    <?php

    }




    public function search()
    {
        ?>
        <form action="./" method="get">
        <input type="text" name="id" id="id" placeholder="identifiant article" required>
        <input type="submit" value="accéder">
        </form>
        <?php

    }

    public function tag($getlist, $tag)
    {
        echo '<div class="tag">';
        echo '<ul>';
        foreach ($getlist as $item) {
            if (in_array($tag, $item->tag('array'))) {
                echo '<li><a href="?id=' . $item->id() . '">' . $item->titre() . '</a> - ' . $item->intro();
                if ($this->session() >= 2) {
                    echo ' - <a href="?id=' . $item->id() . '&edit=1">modifier</a></li>';
                } else {
                    echo '</li>';
                }
            }
        }
        echo ' </ul> ';
        echo ' </div> ';
    }

    public function lien($getlist, $lien)
    {
        echo '<div class="lien">';
        echo '<ul>';
        foreach ($getlist as $item) {
            if (in_array($lien, $item->lien('array'))) {
                echo '<li><a href="?id=' . $item->id() . '">' . $item->titre() . '</a> - ' . $item->intro();
                if ($this->session() >= 2) {
                    echo ' - <a href="?id=' . $item->id() . '&edit=1">modifier</a></li>';
                } else {
                    echo '</li>';
                }
            }
        }
        echo ' </ul> ';
        echo ' </div> ';
    }

    public function home($getlist)
    {
        echo '<ul>';
        foreach ($getlist as $item) {
            echo '<li><a href="?id=' . $item->id() . '">' . $item->titre() . '</a> - ' . $item->intro();
            if ($this->session() >= 2) {
                echo ' - <a href="?id=' . $item->id() . '&edit=1">modifier</a></li>';
            } else {
                echo '</li>';
            }

        }
        echo ' </ul> ';
    }

    public function dump($getlist)
    {
        echo '<ul>';
        foreach ($getlist as $item) {
            echo '<li>';
            var_dump($item);
            echo '</li>';
        }
        echo ' </ul> ';
    }

    public function home2($getlist)
    {
        echo '<div class="home">';
        if ($this->session() >= 2) {
            echo '<ul>';
            foreach ($getlist as $item) {
                $count = 0;

                foreach ($getlist as $lien) {
                    if (in_array($item->id(), $lien->lien('array'))) {
                        $count++;
                    }
                }
                echo '<li><a href="?id=' . $item->id() . '">' . $item->titre() . '</a> - ' . $item->intro();
                echo ' - <a href="?lien=' . $item->id() . '">' . $count . '</a> ';
                echo ' - <a href="?id=' . $item->id() . '&edit=1">modifier</a></li>';
            }
            echo ' </ul> ';
        }
        echo ' </div> ';
    }

    public function home2table($getlist)
    {
        echo '<div class="home">';
        echo '<section>';
        echo '<h1>W</h1>';
        $this->search();
        if ($this->session() >= 2) {
            echo '<h1>Home</h1>';
            echo '<table>';
            echo '<tr><th><a href="./?tri=titre">titre</a></th><th>résumé</th><th>lien from</th><th>lien to</th><th><a href="./?tri=datemodif&desc=DESC">dernière modif</a></th><th><a href="./?tri=datecreation&desc=DESC">date de création</a></th><th>edit</th></tr>';
            foreach ($getlist as $item) {
                $liento = 0;
                $lienfrom = 0;

                foreach ($getlist as $lien) {
                    if (in_array($item->id(), $lien->lien('array'))) {
                        $liento++;
                    }
                }
                foreach ($item->lien('array') as $count) {
                    $lienfrom++;
                }
                echo '<tr>';
                echo '<td><a href="?id=' . $item->id() . '">' . $item->titre() . '</a></td>';
                echo '<td>' . $item->intro() . '</td>';
                echo '<td>' . $lienfrom . '</td>';
                echo '<td><a href="?lien=' . $item->id() . '">' . $liento . '</a></td>';
                echo '<td>' . $item->datemodif('hrdi') . '</td>';
                echo '<td>' . $item->datecreation('hrdi') . '</td>';
                echo '<td><a href="?id=' . $item->id() . '&edit=1">modifier</a></td>';
                echo '</tr>';
            }
            echo ' </table> ';
        }
        echo '</section>';
        echo ' </div> ';
    }

    public function aside($list)
    {
        if ($this->session() >= 2) {
            echo '<aside><ul>';
            foreach ($list as $item) {
                echo '<li><a href="?id=' . $item['id'] . '&edit=1">' . $item['titre'] . '</a> - <code>[' . $item['titre'] . '](?id=' . $item['id'] . ')</code>';


            }
            echo ' </ul></aside> ';
        }
    }

    public function nav($app)
    {
        echo '<nav>';
        echo $this->session();
        echo '</br>';

        echo '<a class="button" href="?">home</a>';

        if ($this->session() == 0) {
            if(isset($_GET['id'])) {
                echo '<form action="./?id='. $_GET['id'].'" method="post">';
            } else {
                echo '<form action="." method="post">';
            }
            ?>
            <input type="hidden" name="action" value="login">
            <input type="password" name="pass" id="pass" placeholder="password">
            <input type="submit" value="login">
            </form>
            <?php

        }
        if ($this->session() > 0) {
            if(isset($_GET['id'])) {
                echo '<form action="./?id='. $_GET['id'].'" method="post">';
            } else {
                echo '<form action="." method="post">';
            }
            ?>
            <input type="hidden" name="action" value="logout">
            <input type="submit" value="logout">
            </form>
            <?php

        }
        if ($this->session() == 2 && isset($_GET['id']) && $app->exist($_GET['id'])) {
            if (isset($_GET['edit']) && $_GET['edit'] == 1) {
                echo '<a class="button" href="?id=' . $_GET['id'] . '" target="_blank">display</a>';
            } else {
                echo '<a class="button" href="?id=' . $_GET['id'] . '&edit=1" >edit</a>';
            }
        }
        if ($this->session() == 2 && !isset($_GET['id'])) {
            echo '<a class="button" href="?aff=media" >Media</a>';
            echo '<a class="button" href="?aff=record" >Record</a>';
            echo '<a class="button" href="?aff=admin" >Admin</a>';
        }

        ?>
        </nav>
        <?php

    }

    // ____________________________________________________ M E D ________________________________________________

    public function media()
    {
        echo '<body>';
        echo '<section>';

        $this->addmedia();
        $this->medialist();

        echo '</section>';
        echo '</body>';

    }

    public function addmedia()
    {
        if ($this->session() >= 2) {

            ?>
            <details close>
                        <summary>Add Media</summary>
            <form action="./" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="addmedia">
            <input type="file" accept="*" name="media" required>
            <input type="text" name="id" id="" placeholder="nom du fichier" required>
            <input type="submit" value="envoi">
            </form>
            </details>
            <?php

        }
    }

    public function medialist(App $app, $dir = "../media/")
    {
        echo '<details open>';
        echo '<summary>Media List</summary>';

        echo '<article class="gest">';

        echo '<form action="" method="post">';

        echo '<ul class="grid">';

        foreach ($app->getlistermedia($dir) as $item) {
            echo '<li class="little">';
            ?>
            <input type="checkbox" id="<?= $item->id() ?>" name="<?= $item->id() ?>" value="1">
            <label for="<?= $item->id() ?>"><?= $item->id() ?></label>
            <input type="hidden" name="id" value="<?= $item->id() ?>">

            <?php

            $filepath = $dir . DIRECTORY_SEPARATOR . $item->id() . '.' . $item->extension();

            echo '<label for="' . $item->id() . '"><img class="thumbnail" src="' . $filepath . '" alt="' . $item->id() . '"></label>';

            echo '<span class="infobulle">';
            echo 'width = ' . $item->width() . ' px';
            echo '<br/>';
            echo 'height = ' . $item->height() . ' px';
            echo '<br/>';
            echo 'filesize = ' . readablesize($item->size());
            echo '<br/>';

            echo '<input type="text" value="![' . $item->id() . '](/' . $item->id() . '.' . $item->extension() . ')">';
            echo '<br/>';


            echo '<a href="' . $filepath . '" target="_blank" ><img  src="' . $filepath . '" alt="' . $item->id() . '"></a>';
            echo '</span>';

            echo '</li>';
        }

        echo '</ul>';

        ?>
        <select name="action" id="">
            <option value="">compress /2</option>
            <option value="">downscale /2</option>
            <option value="">upscale *2</option>
        </select>
        <input type="submit" value="edit">
        <input type="submit" value="delete">
        </form>
        </div>


        <?php


        echo '</article>';
        echo '</details>';


    }



   //______________________________________________________ S E T _________________________________________________

    public function setsession($session)
    {
        if ($session <= 2 and $session >= 0) {
            $session = intval($session);
            $this->session = $session;
        }
    }

   //______________________________________________________ G E T _________________________________________________

    public function session()
    {
        return $this->session;
    }


}



?>