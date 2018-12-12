<?php

if ($app->exist($_GET['id'])) {

    $art = $app->get($_GET['id']);

    if (isset($_GET['edit']) and $_GET['edit'] == 1 and $app->session() >= $app::EDITOR) {
        echo '<main class=edit>';
        $aff->edit($art, $app, $app->getlister(['id', 'title']), $config->fontsize(), $app->getlistermedia($app::MEDIA_DIR, 'image'));
        $aff->aside($app);
        echo '</main>';
    } else {
        echo '<main class="lecture">';


        $art->autotaglistupdate($app->taglist($app->getlister(['id', 'title', 'description', 'tag']), $art->autotaglist()));


        $aff->lecture($art, $app);
        echo '</main>';

    }
} else {
    echo '<span class="alert">This article does not exist yet</span>';

    if ($app->session() >= $app::EDITOR) {
        echo '<form action="?id=' . $_GET['id'] . '&edit=1" method="post"><input type="hidden" name="action" value="new"><input type="submit" value="Create"></form>';
    }

}

?>