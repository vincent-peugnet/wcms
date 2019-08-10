<?php

$this->layout('readerlayout') ?>

<?php
$this->start('head');
?>

<head>
    <?= Config::alertcss() ? '<link href="' . Model::globalpath() . 'global.css" rel="stylesheet" />' : '' ?>
</head>


<?php
$this->stop();
?>



<?php $this->start('page') ?>

<body class="alert">

<main class="alert">

    <?php
    if ($readernav) {
        $this->insert('navart', ['user' => $user, 'art' => $art, 'artexist' => $artexist, 'canedit' => $canedit]);
    }

    ?>




    <?= !empty(Config::alerttitle()) ? '<h1>' . Config::alerttitle() . '</h1>' : '' ?>

    <?php

    $form = '<p>
    <form action="' . $this->url('log') .'" method="post">
    <input type="password" name="pass" id="loginpass" placeholder="password">
    <input type="hidden" name="route" value="artread/">
    <input type="hidden" name="id" value="' . $art->id() . '">
    <input type="submit" name="log" value="login" id="button">
    </form>
    </p>';


    if(!$artexist) {
        if(!empty(Config::existnot())) {
            echo '<h2>' . Config::existnot() . '</h2>';
        }
        if(Config::existnotpass() && !$canedit) {
            echo $form;
        }        
    } else {



        switch ($art->secure()) {
            case 1:
                if(!empty(Config::private())) {
                    echo '<h2>' . Config::private() . '</h2>';
                }
                if(Config::privatepass()) {
                    echo $form;
                }
                break;
            
            case 2:
                if(!empty(Config::notpublished())) {
                    echo '<h2>' . Config::notpublished() . '</h2>';
                }
                if(Config::notpublishedpass()) {
                    echo $form;
                }
                break;
        }
    }



    if ($canedit) {
        ?>
        <p><a href="<?= $this->uart('artadd', $art->id()) ?>">⭐ Create</a></p>            
        <?php
        } elseif(!empty(Config::alertlink())) {
            ?>
            <p><a href="<?= $this->uart('artread/', Config::alertlink()) ?>"><?= empty(Config::alertlinktext()) ? Config::alertlink() : Config::alertlinktext() ?></a></p>
            <?php
        }


    ?>



</main>


</body>

<?php $this->stop() ?>