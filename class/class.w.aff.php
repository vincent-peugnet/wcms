<?php

class Aff
{

    private $session;

    private static $edit = 2;


    // ________________________________________________ C O N S T R U C T ______________________________________________


    public function __construct($session = 0)
    {
        $this->setsession($session);
    }



    // ____________________________________________________ C O N F I G ______________________________________________


    public function configform()
    {
        ?>
        <p>Config file does not exist yet, or maybe you deleted it ? Anyway, it is time to set it :</p>
        <form action="" method="post">
        <input type="hidden" name="config" value="create">
        Database settings</br>
        <input type="text" name="host" id="" placeholder="host"></br>
        <input type="text" name="dbname" id="" placeholder="dbname"></br>
        <input type="text" name="user" id="" placeholder="user"></br>
        <input type="text" name="password" id="" placeholder="password"></br>
        Domain name settings</br>
        <input type="text" name="domain" id="" placeholder="domain"></br>
        W_cms settings</br>
        <input type="text" name="admin" id="" placeholder="W admin password" required></br>
        <input type="hidden" name="editor" id="" value="editor">
        <input type="hidden" name="invite" id="" value="invite">
        <input type="hidden" name="read" id="" value="read">
        <input type="hidden" name="cssread" id="" value="">
        (You can change everything later, set at least your admin password, and don't forget it !)</br>
        <input type="submit" value="create config file">
        </form>
        <?php

    }







    // ____________________________________________________ F U N ______________________________________________


    public function lecture(Art $art, App $app)
    {
        if ($art->secure() == 1) {
            echo '<span class="alert">This article is private</span>';
        }
        if ($art->secure() == 2) {
            echo "<span class=\"alert\">This article is not published yet</span>";
        }

        if ($app->session() >= $art->secure()) {
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
            
            section a.external {
                color: <?= $art->couleurlienblank() ?>;
            }
            <?= $art->csstemplate($app) ?>
            </style>
            <header>
            <h1><?= $art->titre() ?></h1>
            <h6><?= $art->soustitre() ?></h6>
            </header>
            <article><?= $art->html($app) ?></article>
            <?php

        }
    }

    public function edit(Art $art, App $app, $list)
    {
        if ($app->session() >= self::$edit) {

            ?>
                   
            <form action="?id=<?= $art->id() ?>" method="post" id="artedit">
            <textarea style="background-color: <?= $art->couleurbkg() ?>; color: <?= $art->couleurtext() ?>;" name="html" id="html" spellcheck="true" autofocus><?= $art->md(); ?></textarea>


                <details id="editinfo" close>
                    <summary>Infos</summary>
                    <fieldset>                        
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
                    </fieldset>
                </details>
                <details id="editcss" close>
                    <summary>CSS</summary>
                    <fieldset>
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
                </fieldset>
                </details>

                <input type="hidden" name="datecreation" value="<?= $art->datecreation('string'); ?>">
                <input type="hidden" name="id" value="<?= $art->id() ?>">
            </form>
            <div id="submit">
                <a href="./">↖</a>
                    <input type="submit" name="action" value="update" accesskey="s" onclick="document.getElementById('artedit').submit();" form="artedit">
                    <input type="submit" name="action" value="delete" onclick="confirmSubmit(event, 'Delete this article', 'artedit')" form="artedit">
                    <a href="?id=<?= $art->id() ?>" target="_blank">👁</a>
                </div>


    <?php

}

}



public function copy(Art $art, $list)
{
    ?>
    <div id="copy">
        <form action="?id=<?= $art->id() ?>&edit=1" method="post">
            <fieldset>
            <input type="hidden" name="action" value="copy">
                <input type="hidden" name="id" value="<?= $art->id() ?>">
                <select name="copy" required>
                    <?php
                    foreach ($list as $item) {
                        echo '<option value="' . $item->id() . '">' . $item->id() . '</option>';
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
                <input type="submit" value="copy" onclick="confirmSubmit(event, 'Erase values')">
            </fieldset>
        </form>
    </div>
    <?php

}

public function head($title, $tool, $color4)
{
    ?>
    <head>
        <meta charset="utf8" />
        <meta name="viewport" content="width=device-width" />
        <link rel="shortcut icon" href="../media/logo.png" type="image/x-icon">
        <link href="/css/stylebase.css" rel="stylesheet" />
        <link href="/css/style<?= $tool ?>.css" rel="stylesheet" />
        <style>
            :root {
                --color4: <?= $color4 ?>;
            }
        </style>
        <title><?= $title ?></title>
        <script src="../rsc/js/app.js"></script>
    </head>
    <?php

}

public function arthead(Art $art, $cssdir, $cssread = '', $edit = 0)
{
    ?>
    <head>
        <meta charset="utf8" />
        <meta name="description" content="<?= $art->intro() ?>" />
        <meta name="viewport" content="width=device-width" />
        <link rel="shortcut icon" href="../media/logo.png" type="image/x-icon">
        <link href="/css/stylebase.css" rel="stylesheet" />
        <?= $edit == 0 ? '<link href="' . $cssdir . $cssread . '" rel="stylesheet" />' : '<link href="/css/styleedit.css" rel="stylesheet" />' ?>
        <title><?= $edit == 1 ? '✏' : '' ?> <?= $art->titre() ?></title>
        <script src="../rsc/js/app.js"></script>
    </head>
    <?php

}

public function noarthead($id, $cssdir, $cssread = '')
{
    ?>
    <head>
        <meta charset="utf8" />
        <meta name="description" content="This article does not exist yet." />
        <meta name="viewport" content="width=device-width" />
        <link rel="shortcut icon" href="../media/logo.png" type="image/x-icon">
        <link href="/css/stylebase.css" rel="stylesheet" />
        <link href="<?= $cssdir . $cssread ?>" rel="stylesheet" />
        <title>❓ <?= $id ?></title>
        <script src="../rsc/js/app.js"></script>
    </head>
    <?php

}




public function search()
{
    ?>
    <div id="search">        
        <form action="./" method="get">
        <input type="text" name="id" id="id" placeholder="identifiant article" required>
        <input type="submit" value="accéder">
    </form>
    </div>
    <?php

}

public function tag($getlist, $tag, $app)
{
    echo '<div class="tag">';
    echo '<ul>';
    foreach ($getlist as $item) {
        if (in_array($tag, $item->tag('array'))) {
            echo '<li><a href="?id=' . $item->id() . '">' . $item->titre() . '</a> - ' . $item->intro();
            if ($app->session() >= $app::EDITOR) {
                echo ' - <a href="?id=' . $item->id() . '&edit=1">modifier</a></li>';
            } else {
                echo '</li>';
            }
        }
    }
    echo ' </ul> ';
    echo ' </div> ';
}

public function lien($getlist, $lien, App $app)
{
    echo '<div class="lien">';
    echo '<ul>';
    foreach ($getlist as $item) {
        if (in_array($lien, $item->lien('array'))) {
            echo '<li><a href="?id=' . $item->id() . '">' . $item->titre() . '</a> - ' . $item->intro();
            if ($app->session() >= $app::EDITOR) {
                echo ' - <a href="?id=' . $item->id() . '&edit=1">modifier</a> - <a href="?lien=' . $item->id() . '">liens</a></li>';
            } else {
                echo '</li>';
            }
        }
    }
    echo ' </ul> ';
    echo ' </div> ';
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

public function header()
{
    echo '<header>';
    $this->search();
    echo '</header>';
}


public function home2table(App $app, $getlist, $masslist)
{
    echo '<div id="main">';
    echo '<h2>Articles</h2>';
    echo '<form action="./" method="post">';

    ?>
    <div id="massedit">
        <h3>Mass Edit</h3>
    <select name="massedit" required>
        <option value="public">set as public</option>
        <option value="private">set as private</option>
        <option value="not published">set as not published</option>
        <option value="erasetag">erase all tags</option>
        <option value="erasetemplate">erase template</option>
        <option value="delete">delete</option>
    </select>

    <input type="submit" name="massaction" value="do" onclick="confirmSubmit(event, 'Are you sure')" >

    <input type="text" name="targettag" placeholder="add tag">
    <input type="submit" name="massaction" value="add tag" onclick="confirmSubmit(event, 'Are you sure')" >

    <select name="masstemplate">
        <?php
        foreach ($masslist as $art) {
            echo '<option value="' . $art->id() . '">' . $art->id() . '</option>';
        }
        ?>
    </select>

    <input type="submit" name="massaction" value="set template" onclick="confirmSubmit(event, 'Are you sure')" >

    <input type="hidden" name="action" value="massedit">
    </div>

    <?php
    if ($app->session() >= $app::EDITOR) {
        echo '<table id="home2table">';
        echo '<tr><th>x</th><th>title</th><th>tag</th><th>summary</th><th>↘ to</th><th>↗ from</th><th>last modification</th><th>date of creation</th><th>privacy</th><th>display</th></tr>';
        foreach ($getlist as $item) {
            echo '<tr>';
            echo '<td><input type="checkbox" name="id[]" value=' . $item->id() . '></td>';
            echo '<td><a href="?id=' . $item->id() . '&edit=1">' . $item->titre() . '</a></td>';
            echo '<td>' . $item->tag('sort') . '</td>';
            echo '<td>' . $item->intro() . '</td>';
            echo '<td><a href="?lien=' . $item->id() . '">' . $item->liento('sort') . '</a></td>';
            echo '<td>' . $item->lien('sort') . '</td>';
            echo '<td>' . $item->datemodif('hrdi') . '</td>';
            echo '<td>' . $item->datecreation('hrdi') . '</td>';
            echo '<td>' . $item->secure('string') . '</td>';
            echo '<td><a href="?id=' . $item->id() . '" target="_blank">👁</a></td>';
            echo '</tr>';
        }
        echo ' </table> ';
        echo ' </form> ';
        echo '</div>';
    }
}

public function option(App $app, Opt $opt)
{
    if ($app->session() >= $app::EDITOR) {
        echo '<div id="options">';
        echo '<h2>Options</h2>';
        echo '<form action="./" method="get" >';
        echo '<input type="submit" name="submit" value="filter">';
        echo '⬅<input type="submit" name="submit" value="reset">';


        $this->optionsort($opt);
        $this->optionprivacy($opt);
        $this->optiontag($opt);

        if ($opt->invert() == 1) {
            echo '<input type="checkbox" name="invert" value="1" id="invert" checked>';
        } else {
            echo '<input type="checkbox" name="invert" value="1" id="invert">';
        }
        echo '<label for="invert">invert</></br>';


        echo '<input type="submit" name="submit" value="filter">';
        echo '⬅<input type="submit" name="submit" value="reset">';

        echo '</form></div>';

    }

}

public function optiontag(Opt $opt)
{

    echo '<fieldset><legend>Tag</legend><ul>';

    echo '<input type="radio" id="OR" name="tagcompare" value="OR" ' . ($opt->tagcompare() == "OR" ? "checked" : "") . ' ><label for="OR">OR</label>';
    echo '<input type="radio" id="AND" name="tagcompare" value="AND" ' . ($opt->tagcompare() == "AND" ? "checked" : "") . '><label for="AND">AND</label>';

    $in = false;
    $out = false;
    $limit = 1;
    foreach ($opt->taglist() as $tagfilter => $count) {

        if ($count > $limit && $in == false) {
            echo '<details open><summary>>' . $limit . '</summary>';
            $in = true;
        }
        if ($count == $limit && $in == true && $out == false) {
            echo '</details><details><summary>' . $limit . '</summary>';
            $out = true;
        }

        if (in_array($tagfilter, $opt->tagfilter())) {

            echo '<li><input type="checkbox" name="tagfilter[]" id="' . $tagfilter . '" value="' . $tagfilter . '" checked /><label for="' . $tagfilter . '">' . $tagfilter . ' (' . $count . ')</label></li>';
        } else {
            echo '<li><input type="checkbox" name="tagfilter[]" id="' . $tagfilter . '" value="' . $tagfilter . '" /><label for="' . $tagfilter . '">' . $tagfilter . ' (' . $count . ')</label></li>';
        }
    }
    if ($in = true || $out = true) {
        echo '</details>';
    }
    echo '</ul></fieldset>';

}

public function optionprivacy(Opt $opt)
{
    echo '<fieldset><legend>Privacity</legend><ul>';
    echo '<li><input type="radio" id="4" name="secure" value="4" ' . ($opt->secure() == 4 ? "checked" : "") . ' /><label for="4">all</label></li>';
    echo '<li><input type="radio" id="2" name="secure" value="2" ' . ($opt->secure() == 2 ? "checked" : "") . ' /><label for="2">not published</label></li>';
    echo '<li><input type="radio" id="1" name="secure" value="1" ' . ($opt->secure() == 1 ? "checked" : "") . ' /><label for="1">private</label></li>';
    echo '<li><input type="radio" id="0" name="secure" value="0" ' . ($opt->secure() == 0 ? "checked" : "") . ' /><label for="0">public</label></li>';
    echo '</ul></fieldset>';
}

public function optionsort(Opt $opt)
{
    echo '<fieldset><legend>Sort</legend>';
    echo '<select name="sortby" id="sortby">';
    foreach ($opt->col('array') as $key => $col) {
        echo '<option value="' . $col . '" ' . ($opt->sortby() == $col ? "selected" : "") . '>' . $col . '</option>';
    }
    echo '</select>';
    echo '</br>';
    echo '<input type="radio" id="asc" name="order" value="1" ' . ($opt->order() == '1' ? "checked" : "") . ' /><label for="asc">ascending</label>';
    echo '</br>';
    echo '<input type="radio" id="desc" name="order" value="-1" ' . ($opt->order() == '-1' ? "checked" : "") . ' /><label for="desc">descending</label>';

    echo '</fieldset>';

}

public function map(App $app, $url)
{
    echo '<div class="home"><section>';

    $map = "";
    $link = "";
    foreach ($app->getlister(['id', 'lien']) as $item) {
        foreach ($item->lien('array') as $lien) {
            $map = $map . ' </br> ' . $item->id() . ' --> ' . $lien;
        }
        $link = $link . '</br>click ' . $item->id() . ' "' . $url . '/w/?id=' . $item->id() . '"';
    }
    echo $map;
    echo $link;

    echo '</div></section>';
}

public function aside(App $app)
{
    if ($app->session() >= $app::EDITOR) {
        echo '<div id="linklist">Links<div id="roll"><ul>';
        foreach ($app->lister() as $item) {
            echo '<li><a href="?id=' . $item['id'] . '&edit=1">' . $item['titre'] . '</a> - <input type="text" value="[' . $item['titre'] . '](?id=' . $item['id'] . ')">';


        }
        echo ' </ul></div></div> ';
    }
}

public function nav($app)
{
    echo '<nav>';
    echo $app->session();
    echo '<div id="menu">';

    echo '<a class="button" href="?">home</a>';

    if ($app->session() == $app::FREE) {
        if (isset($_GET['id'])) {
            echo '<form action="./?id=' . $_GET['id'] . '" method="post">';
        } else {
            echo '<form action="." method="post">';
        }
        ?>
            <input type="hidden" name="action" value="login">
            <input type="password" name="pass" id="loginpass" placeholder="password">
            <input type="submit" value="login">
            </form>
            <?php

        }
        if ($app->session() > $app::FREE) {
            if (isset($_GET['id'])) {
                echo '<form action="./?id=' . $_GET['id'] . '" method="post">';
            } else {
                echo '<form action="." method="post">';
            }
            ?>
            <input type="hidden" name="action" value="logout">
            <input type="submit" value="logout">
            </form>
            <?php

        }
        if ($app->session() >= $app::EDITOR && isset($_GET['id']) && $app->exist($_GET['id'])) {
            if (isset($_GET['edit']) && $_GET['edit'] == 1) {
                echo '<a class="button" href="?id=' . $_GET['id'] . '" target="_blank">display</a>';
            } else {
                echo '<a class="button" href="?id=' . $_GET['id'] . '&edit=1" >edit</a>';
            }
        }
        if ($app->session() >= $app::EDITOR) {
            echo '<a class="button" href="?aff=media" >Media</a>';
            echo '<a class="button" href="?aff=record" >Record</a>';
            if ($app->session() >= $app::ADMIN) {
                echo '<a class="button" href="?aff=admin" >Admin</a>';
            }
        }




        ?>
        </div>
        </nav>
        <?php

    }

    // ____________________________________________________ M E D ________________________________________________


    public function addmedia($app)
    {
        if ($app->session() >= $app::EDITOR) {

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

    public function medialist(array $getlistermedia, $dir)
    {
        ?>
        <details open>
        <summary>Media List</summary>

        <form action="" method="post">

        <table id=mediatable>
            <tr><th>x</th><th>Name</th><th>extension</th><th>width</th><th>height</th><th>size</th><th>code</th><th>thumbnail</th></tr>
        <?php


        foreach ($getlistermedia as $item) {
            $filepath = $dir . $item->id() . '.' . $item->extension();
            echo '<tr>';
            echo '<td><input type="checkbox" name="id[]" value=' . $item->id() . ' id="' . $item->id() . '"></td>';
            echo '<td><label for="' . $item->id() . '">' . $item->id() . '</label></td>';
            echo '<td>' . $item->extension() . '</td>';
            echo '<td>' . $item->width() . '</td>';
            echo '<td>' . $item->height() . '</td>';
            echo '<td>' . readablesize($item->size()) . '</td>';
            if ($item->type() == 'image') {
                echo '<td><input type="text" value="![' . $item->id() . '](/' . $item->id() . '.' . $item->extension() . ')"></td>';
                echo '<td class="tooltip">👁<span class="infobulle"><a href="' . $filepath . '" target="_blank" ><img class="thumbnail" src="' . $filepath . '" alt="' . $item->id() . '"></a></span></td>';
            } elseif ($item->type() == 'sound') {
                echo '<td><input type="text" value="[' . $item->id() . '](' . $filepath . ')"></td>';
                echo '<td><a href="' . $filepath . '" target="_blank" >♪</a></td>';
            } else {
                echo '<td><input type="text" value="[' . $item->id() . '](' . $filepath . ')"></td>';
                echo '<td><a href="' . $filepath . '" target="_blank" >∞</a></td>';
            }
            echo '</tr>';
            echo '';

        }


        ?>
        
        
        </table>

        <select name="action" id="">
            <option value="">compress /2</option>
            <option value="">downscale /2</option>
            <option value="">upscale *2</option>
        </select>
        <input type="submit" value="edit">
        <input type="submit" value="delete">
        </form>
        </div>

        </details>


        <?php

    }


    //______________________________________________________ R E C _________________________________________________


    public function recordlist(App $app, $dir = "../ACRRecordings/")
    {
        echo '<details open>';
        echo '<summary>Media List</summary>';

        echo '<article class="gest">';

        echo '<form action="" method="post">';

        echo '<ul>';

        foreach ($app->getlisterrecord($dir) as $item) {
            echo '<li>';

            ?>
            <input type="checkbox" id="<?= $item->id() ?>" name="<?= $item->id() ?>" value="1">
            <label for="<?= $item->id() ?>"><?= $item->id() ?></label>
            <input type="hidden" name="id" value="<?= $item->id() ?>">

            <?php

            $filepathurl = $dir . urlencode($item->id()) . '.' . $item->extension();

            echo '<br/>';
            var_dump($item->size());
            var_dump(intval($item->size()));
            echo 'filesize = ' . readablesize(intval($item->size()));
            echo '<br/>';
            echo 'extension = ' . $item->extension();
            echo '<br/>';

            ?>

            <audio controls>
            <source src="<?= $filepathurl ?>" type="audio/mpeg">
            </audio>



            <?php




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


    //______________________________________________________ A D M _________________________________________________



    public function admincss(Config $config, $app)
    {
        ?>
        <article>
        <h2>CSS</h2>
        <p>Current global css : <strong><?= $config->cssread() ?></strong></p>
        <details colse>
        <summary>Default CSS</summary>

        <p>This CSS will apply to all your articles.</p>

        <form action="?aff=admin" method="post" >
        <input type="hidden" name="action" value="editconfig">
        <select name="cssread" required>

        <?php
        foreach ($app->dirlist($app::CSS_READ_DIR, 'css') as $item) {
            if ($item == $config->cssread()) {
                echo '<option value="' . $item . '" " selected >' . $item . '</option>';
            } else {
                echo '<option value="' . $item . '">' . $item . '</option>';
            }
        }
        ?>
        </select>
        <input type="submit" value="choose">
        </form>
        </details>


        <?php 
        $cssfile = $app::CSS_READ_DIR . $config->cssread();
        if (is_file($cssfile)) {
            $cssread = file_get_contents($cssfile);
            echo '<details>';
            echo '<summary>Edit current CSS</summary>';
            echo '<form action="./" method="post">';
            echo '<textarea name="editcss" id="cssarea">' . $cssread . '</textarea>';
            echo '<input type="hidden" name="action" value="editcss">';
            echo '<input type="submit" value="edit">';
            echo '</form>';
            echo '</details>';
        }

        ?>
        <details close>
        <summary>Add CSS file</summary>
        <form action="./" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="addcss">
        <input type="file" accept=".css" name="css" required>
        <input type="text" name="id" id="" placeholder="filename" required>
        <input type="submit" value="submit">
        </form>
        </details>

        </article>
        <?php

    }

    public function admindb($config)
    {
        ?>

        <article>


        <h2>Database</h2>

        <details>
        <summary>Database credentials</summary>

        <p>Fill this sections with the database settings you want to connect to</p>

        <form action="./" method="post">
        <input type="hidden" name="action" value="editconfig">
        <label for="host">Host</label>
        <input title="host" type="text" name="host" id="host" value="<?= $config->host() ?>">
        <label for="dbname">DataBase name</label>
        <input title="dbname" type="text" name="dbname" id="dbname" value="<?= $config->dbname() ?>">
        <label for="user">User name</label>
        <input title="user" type="text" name="user" id="user" value="<?= $config->user() ?>">
        <label for="password">Password</label>
        <input title="password" type="text" name="password" id="password" value="<?= $config->password() ?>">
        <input type="submit" value="edit" id="">
        </form>

        </details>


        </article>

        <?php

    }

    public function adminpassword(Config $config)
    {
        ?>
        <article>

        <h2>Passwords</h2>

        <details>
        <summary>Admin</summary>

        <p>Edit your own admin password. You can find it in the config.json file, in the root of your website folder.</p>

        <form action="./" method="post">
        <input type="hidden" name="action" value="editconfig">
        <label for="admin">Administrator password (10)</label>
        <input title="admin password" type="password" name="admin" id="admin" value="<?= $config->admin() ?>" >
        <input type="submit" value="edit" id="">
        </form>

        </details>
        <details>
        <summary>Others</summary>

        <p>Use this section to set all the others users passwords. They cant access this page, so they cant change it by themselves.</p>

        <form action="./" method="post">
        <input type="hidden" name="action" value="editconfig">
        <label for="editor">Editor password (3)</label>
        <input title="editor" type="text" name="editor" id="editor" value="<?= $config->editor() ?>">
        <label for="invite">Invite password (2)</label>
        <input title="invite" type="text" name="invite" id="invite" value="<?= $config->invite() ?>" >
        <label for="read">Reader password (1)</label>
        <input title="read" type="text" name="read" id="read" value="<?= $config->read() ?>">
        <input type="submit" value="edit" id="">
        </form>

        </details>
        </article>




        <?php

    }

    public function admintable(Config $config, string $status, array $arttables)
    {
        ?>

        <article>

            <h2>Table</h2>

        

        <p>Database status : <strong><?= $status ?></strong></p>


        <p>Current Table : <strong><?= $config->arttable(); ?></strong></p>
        <details>
        <summary>Select Table</summary>
        <p>The table is where all your articles are stored, select the one you want to use.</p>

        <form action="./" method="post">
        <select name="arttable" required>

        <?php
        foreach ($arttables as $arttable) {
            if ($arttable == $config->arttable()) {
                echo '<option value="' . $arttable . '" " selected >' . $arttable . '</option>';
            } else {
                echo '<option value="' . $arttable . '">' . $arttable . '</option>';
            }
        }
        ?>
        </select>
        <input type="hidden" name="action" value="editconfig">
        <input type="submit" value="choose">
        </form>

        </details>

        <details>
        <summary>Add table</summary>

        <p>Create new table in your database. You need at least one to use W_cms</p>

        <form action="./" method="post">
        <input type="hidden" name="actiondb" value="addtable">
        <input type="text" name="tablename" placeholder="table name" maxlength="30" required>
        <input type="submit" value="create">
        </form>

        </details>

        <details>
            <summary>Duplicate Table</summary>
        <p>If you want to save versions of your work.</p>

        <form action="./" method="post">
        <label for="arttable">Select the table you want to copy.</label>
        <select name="arttable" id="arttable" required>

        <?php
        foreach ($arttables as $arttable) {
            if ($arttable == $config->arttable()) {
                echo '<option value="' . $arttable . '" " selected >' . $arttable . '</option>';
            } else {
                echo '<option value="' . $arttable . '">' . $arttable . '</option>';
            }
        }
        ?>
        </select>
        <label for="tablename">Choose a name for the copy</label>
        <input type="text" name="tablename" id="tablename" required>
        <input type="hidden" name="actiondb" value="duplicatetable">
        <input type="submit" value="Duplicate">
        </form>

        </details>

        </article>

        <?php

    }

    public function admindisplay($color4)
    {
        ?>
        <article>
        <h2>Display</h2>
        <details>
            <summary>Update favicon</summary>
        <form action="./" method="post"  enctype="multipart/form-data">
            <input type="file" name="favicon" id="favicon">
            <input type="submit" value="update">
        </form>
        </details>
        <details>
            <summary>Change desktop background color</summary>
            <form action="./" method="post">
            <label for="color4">Background color</label>
            <input type="color" name="color4" id="color4" value="<?= $color4 ?>">
            <input type="hidden" name="action" value="editconfig">
            <input type="submit" value="color my life">
            </form>
        </details>
        </article>
        <?php

    }





//______________________________________________________ S E T _________________________________________________

    public function setsession($session)
    {
        if ($session <= 100 and $session >= 0) {
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