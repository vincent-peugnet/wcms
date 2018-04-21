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
        if ($art->secure() == 1) {
            echo '<span class="alert"><h4>cet article est privé</h4></span>';
        }
        if ($art->secure() == 2) {
            echo "<span class=\"alert\"><h4>cet article n'est pas publié</h4></span>";
        }

        if ($this->session() >= $art->secure()) {
            ?>
            <style type="text/css">
            body{
                background: <?= $art->couleurbkg() ?>;
            }
            section {
                color: <?= $art->couleurtext() ?>;			
            }
            
            a {
                color: <?= $art->couleurlien() ?>;
            }

            a[target="_blank"] {
                color: <?= $art->couleurlienblank() ?>;
            }
            <?= $art->css() ?>
            </style>
            <section>
            <h1><?= $art->titre() ?></h1>
            <h6><?= $art->soustitre() ?></h6>
            <article><?= $app->introlien($art) ?></article>
            </section>
            <?php

        }
    }

    public function edit(Art $art)
    {
        if ($this->session() >= self::$edit) {

            ?>
		<section class="edit">
            <form action="?id=<?= $art->id() ?>" method="post">
                <label for="titre">Titre :</label>
                <input type="text" name="titre" id="titre" value="<?= $art->titre(); ?>">
                <label for="soustitre">Sous-titre :</label>
                <input type="text" name="soustitre" id="soustitre" value="<?= $art->soustitre(); ?>">
                <label for="intro">Introduction :</label>
                <input type="text" name="intro" id="intro" value="<?= $art->intro(); ?>">
                <label for="tag">Tag(s) :</label>
                <input type="text" name="tag" id="tag" value="<?= $art->tag('string'); ?>">
                <label for="css">Styles CSS :</label>
                <textarea name="css" id="css"><?= $art->css(); ?></textarea>
                <label for="secure">Niveau de sécurité :</label>
                <select name="secure" id="secure">
                    <option value="0" <?= $art->secure() == 0 ? 'selected' : '' ?>>0</option>
                    <option value="1" <?= $art->secure() == 1 ? 'selected' : '' ?>>1</option>
                    <option value="2" <?= $art->secure() == 2 ? 'selected' : '' ?>>2</option>
                </select>
                <label for="couleurtext">Couleur du texte :</label>
                <input type="color" name="couleurtext" value="<?= $art->couleurtext() ?>" id="couleurtext">
                <label for="couleurbkg">Couleur de l'arrière plan :</label>
                <input type="color" name="couleurbkg" value="<?= $art->couleurbkg() ?>" id="couleurbkg">
                <label for="couleurlien">Couleur des liens :</label>
                <input type="color" name="couleurlien" value="<?= $art->couleurlien() ?>" id="couleurlien">
                <label for="couleurlienblank">Couleur des liens externes :</label>
                <input type="color" name="couleurlienblank" value="<?= $art->couleurlienblank() ?>" id="couleurlienblank">
                <label for="html">Contenu :</label>
                <textarea name="html" id="html" ><?= $art->html('md'); ?></textarea>
                <input type="hidden" name="datecreation" value="<?= $art->datecreation('string'); ?>">
                <input type="hidden" name="id" value="<?= $art->id() ?>">
                <div class="submit">
                    <input type="submit" name="action" value="update">
                    <input type="submit" name="action" value="delete" onclick="confirmSubmit(event, 'Suppression de cet article')">
        </div>
            </form>
		</section>

		<?php

}

}

public function head($title)
{
    ?>
        <head>
            <meta charset="utf8" />
		    <meta name="viewport" content="width=device-width" />            
            <link href="/css/style.css" rel="stylesheet" />
            <title><?= $title ?></title>
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
    }

    public function lien($getlist, $lien)
    {
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
    }

    public function home2table($getlist)
    {
        if ($this->session() >= 2) {
            echo '<h1>Home</h1>';
            echo '<table>';
            echo '<tr><th>titre</th><th>résumé</th><th>lien</th><th>edit</th></tr>';
            foreach ($getlist as $item) {
                $count = 0;

                foreach ($getlist as $lien) {
                    if (in_array($item->id(), $lien->lien('array'))) {
                        $count++;
                    }
                }
                echo '<tr>';
                echo '<td><a href="?id=' . $item->id() . '">' . $item->titre() . '</a></td>';
                echo '<td>' . $item->intro() . '</td>';
                echo '<td><a href="?lien=' . $item->id() . '">' . $count . '</a></td>';
                echo '<td><a href="?id=' . $item->id() . '&edit=1">modifier</a></td>';
                echo '</tr>';
            }
            echo ' <table> ';
        }
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
        ?>
        <nav>
        <?= $this->session() ?>
        </br>
        <a href="?">home</a>

        </br>
        <?php

        if (isset($_GET['id']) and $app->exist($_GET['id'])) {
            if ($this->session() == 0) {
                ?>
                <form action="?id=<?= $_GET['id'] ?>" method="post">
                <input type="hidden" name="action" value="login">
                <input type="password" name="pass" id="pass" placeholder="password">
                <input type="submit" value="connect">
                </form>
                <?php

            }
            if ($this->session() >= 1) {
                ?>
                <form action="?id=<?= $_GET['id'] ?>" method="post">
                <input type="hidden" name="action" value="logout">
                <input type="submit" value="disconnect">
                </form>
                <?php

                if ($this->session() == 2) {
                    ?>
                    <a href="?id=<?= $_GET['id'] ?>" target="_blank">display</a>
                    </br>
                    <a href="?id=<?= $_GET['id'] ?>&edit=1" >edit</a>
                    <?php

                }

            }
        } else {
            if ($this->session() == 0) {
                ?>
                <form action="?" method="post">
                <input type="hidden" name="action" value="login">
                <input type="password" name="pass" id="pass" placeholder="password">
                <input type="submit" value="connect">
                </form>
                <?php

            }
            if ($this->session() >= 1) {
                ?>
                <form action="?" method="post">
                <input type="hidden" name="action" value="logout">
                <input type="submit" value="disconnect">
                </form>
                <?php

            }
        }
        ?>
        </nav>
        <?php

    }

    public function addmedia()
    {
        if ($this->session() >= 2) {

            ?>
		<h1>Ajouter un media</h1>
        <form action="./" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="addmedia">
		<input type="file" accept="*" name="media" required>
        <input type="text" name="id" id="" placeholder="nom du fichier" required>
		<input type="submit" value="envoi">
		</form>
        <?php

    }

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