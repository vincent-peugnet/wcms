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






    <?= !empty(Config::alerttitle()) ? '<h1>' . Config::alerttitle() . '</h1>' : '' ?>

    <?php

    $form = '<p>
    <form action="' . $this->url('log') .'" method="post">
    <input type="password" name="pass" id="loginpass" placeholder="password">
    <input type="hidden" name="route" value="pageread/">
    <input type="hidden" name="id" value="' . $page->id() . '">
    <input type="submit" name="log" value="login" id="button">
    </form>
    </p>';


    if(!$pageexist) {
        if(!empty(Config::existnot())) {
            echo '<h2>' . Config::existnot() . '</h2>';
        }
        if(Config::existnotpass() && !$canedit) {
            echo $form;
        }        
    } else {



        switch ($page->secure()) {
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
        <p><a href="<?= $this->upage('pageadd', $page->id()) ?>">⭐ Create</a></p>            
        <?php
        } elseif(!empty(Config::alertlink())) {
            ?>
            <p><a href="<?= $this->upage('pageread/', Config::alertlink()) ?>"><?= empty(Config::alertlinktext()) ? Config::alertlink() : Config::alertlinktext() ?></a></p>
            <?php
        }


    ?>



</main>


</body>

<?php $this->stop() ?>