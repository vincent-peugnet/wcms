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

    public function lecture(Art $art)
    {
        if($art->secure() == 1) {
            echo '<span class="alert"><h4>cet article est privé</h4></span>';
        }
        if($art->secure() == 2) {
            echo "<span class=\"alert\"><h4>cet article n'est pas publié</h4></span>";
        }
        
        if ($this->session() >= $art->secure()) {            
            ?>
            <style type="text/css">
            article {
                background: <?= $art->couleurbkg() ?>;
                color: <?= $art->couleurtext() ?>;			
            }
            
            a {
                color: <?= $art->couleurlien() ?>;
            }
            <?= $art->css() ?>
            </style>
            <article>
            <h1><?= $art->titre() ?></h1>
            <h2><?= $art->soustitre() ?></h2>
            <p><?= $art->html('html') ?></p>
            </article>
            <?php

        }
    }

    public function edit(Art $art)
	{
		if ($this->session() >= self::$edit) {

			?>
		<article>
            <form class="edit" action="?id=<?= $art->id() ?>" method="post">
                <input type="submit" value="modifier">
                <label for="titre">Titre :</label>
                <input type="text" name="titre" id="titre" value="<?= $art->titre(); ?>">
                <label for="soustitre">Sous-titre :</label>
                <input type="text" name="soustitre" id="soustitre" value="<?= $art->soustitre(); ?>">
                <label for="intro">Introduction :</label>
                <input type="text" name="intro" id="intro" value="<?= $art->intro(); ?>">
                <label for="tag">Tag(s) :</label>
                <input type="text" name="tag" id="tag" value="<?= $art->tag(); ?>">
                <label for="css">Styles CSS :</label>
                <textarea name="css" id="css"><?= $art->css(); ?></textarea>
                <label for="secure">Niveau de sécuritée :</label>
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
                <label for="html">Contenu :</label>
                <textarea name="html" id="html" ><?= $art->html('md'); ?></textarea>
                <input type="hidden" name="datecreation" value="<?= $art->datecreation('string'); ?>">
                <input type="hidden" name="id" value="<?= $art->id() ?>">
                <input type="hidden" name="action" value="update">
                <input type="submit" value="modifier">
            </form>
		</article>

		<?php

        }

    }

    public function head($title) {
        ?>
        <head>
            <meta charset="utf8" />
            <link href="/css/style.css" rel="stylesheet" />
            <title><?= $title ?></title>
        </head>
        <?php
    }

    public function home($list)
    {
        echo '<ul>';
		foreach ($list as $item) {              
            echo '<li><a href="?id=' . $item['id'] . '">' . $item['titre'] . '</a> - ' . $item['intro'];
            if ($this->session() >= 2) {
                echo ' - <a href="?id=' . $item['id'] . '&edit=1">modifier</a></li>';
            } else {
                echo '</li>';
            }        
		}
		echo ' </ul> ';
    }

    public function aside($list)
    {
        if ($this->session() >= 2) {
            echo '<aside><ul>';
            foreach ($list as $item) {              
                 echo '<li><a href="?id=' . $item['id'] . '&edit=1">' . $item['titre'] . '</a> - <code>[' . $item['titre'] . '](?id=' . $item['id'].')</code>';
                    
                
            }
            echo ' </ul></aside> ';
        }
    }

    public function nav($app)
    {
        ?>
        <nav>
        <?= $this->session() ?>
        <a href="?" >home</a>
        <?php
        if ($this->session() >= 1) {
            if(isset($_GET['id'])){
            ?>
            <form action="?id=<?= $_GET['id'] ?>" method="post">
            <input type="hidden" name="action" value="logout">
            <input type="submit" value="disconnect">
            </form>
            <?php
            }
            if(isset($_GET['id']) AND $app->exist($_GET['id']) AND $this->session() == 2)
            {
                ?>
                    <a href="?id=<?= $_GET['id'] ?>&display=1" target="_blank">display</a>
                    <a href="?id=<?= $_GET['id'] ?>&edit=1" >edit</a>
                <?php
            }
        }
        else
        {            
            if(isset($_GET['id'])){
                ?>
            <form action="?id=<?= $_GET['id'] ?>" method="post">
            <input type="hidden" name="action" value="login">
            <input type="password" name="pass" id="pass" placeholder="password">
            <input type="submit" value="connect">
            </form>
            <?php
            }
        }
        ?>
        </nav>
        <?php
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