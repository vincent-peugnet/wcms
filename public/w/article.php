<?php

if ($app->exist($_GET['id'])) {

    $art = $app->get($_GET['id']);

    if (isset($_GET['edit']) and $_GET['edit'] == 1 and $app->session() >= $app::EDITOR) {
        echo '<section class=edit>';
        $aff->edit($art, $app, $app->getlister(['id', 'titre']));
        $aff->copy($art, $app->getlister(['id', 'titre']));
        $aff->aside($app);
        echo '</section>';
    } else {
        echo '<section class="lecture">';
        $aff->lecture($art, $app);
        echo '</section>';

    }
} else {
    echo '<span class="alert">This article does not exist yet</span>';

    if ($app->session() >= $app::EDITOR) {
        echo '<form action="?id=' . $_GET['id'] . '&edit=1" method="post"><input type="hidden" name="action" value="new"><input type="submit" value="Create"></form>';
    }

}

?>