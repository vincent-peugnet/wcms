<?php

$this->layout('readerlayout') ?>

<?php
$this->start('head');
?>

<head>
    <?= Wcms\Config::alertcss() ? '<link href="' . Wcms\Model::globalpath() . 'global.css" rel="stylesheet" />' : '' ?>
</head>


<?php
$this->stop();
?>



<?php $this->start('page') ?>

<body class="alert">

<main class="alert">






    <?= !empty(Wcms\Config::alerttitle()) ? '<h1>' . Wcms\Config::alerttitle() . '</h1>' : '' ?>

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
        if(!empty(Wcms\Config::existnot())) {
            echo '<h2>' . Wcms\Config::existnot() . '</h2>';
        }
        if(Wcms\Config::existnotpass() && !$canedit) {
            echo $form;
        }        
    } else {



        switch ($page->secure()) {
            case 1:
                if(!empty(Wcms\Config::private())) {
                    echo '<h2>' . Wcms\Config::private() . '</h2>';
                }
                if(Wcms\Config::privatepass()) {
                    echo $form;
                }
                break;
            
            case 2:
                if(!empty(Wcms\Config::notpublished())) {
                    echo '<h2>' . Wcms\Config::notpublished() . '</h2>';
                }
                if(Wcms\Config::notpublishedpass()) {
                    echo $form;
                }
                break;
        }
    }



    if ($canedit) {
        ?>
        <p><a href="<?= $this->upage('pageadd', $page->id()) ?>">⭐ Create</a></p>            
        <?php
        } elseif(!empty(Wcms\Config::alertlink())) {
            ?>
            <p><a href="<?= $this->upage('pageread/', Wcms\Config::alertlink()) ?>"><?= empty(Wcms\Config::alertlinktext()) ? Wcms\Config::alertlink() : Wcms\Config::alertlinktext() ?></a></p>
            <?php
        }


    ?>



</main>


</body>

<?php $this->stop() ?>