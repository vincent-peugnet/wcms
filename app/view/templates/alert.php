<?php

$this->layout('readerlayout') ?>

<?php
$this->start('head');
?>

<?= Wcms\Config::alertcss() ? '<link href="' . Wcms\Model::dirtopath(Wcms\Model::ASSETS_CSS_DIR) . 'global.css" rel="stylesheet" />' : '' ?>
<meta name="viewport" content="width=device-width">


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
    <input type="text" name="user" id="loginuser" autofocus placeholder="user" required>
    <input type="password" name="pass" id="loginpass" placeholder="password" required>
    <input type="hidden" name="route" value="pageread/">
    <input type="hidden" name="id" value="' . $page->id() . '">
    <input type="checkbox" name="rememberme" id="rememberme" value="1">
    <label for="rememberme">Remember me</label>
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
        <style>
            code {
                font-size: 1.1em;
                border: 1px solid grey;
                padding: 2px 4px;
                margin: 0 5px;
            }
        </style>
        <p><a href="<?= $this->upage('pageadd', $page->id()) ?>">⭐ Create</a></p>

        <p>
            💡 To create a page in one command, you can type
            <code><?= $this->upage('pageadd', $page->id()) ?></code>
            directly in your address bar.
        </p>
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
