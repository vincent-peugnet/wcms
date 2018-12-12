<?php


if ($app->session() >= $app::EDITOR) {

    if ($_GET['aff'] == 'admin' && $app->session() >= $app::ADMIN) {
        echo '<main>';
        echo '<h1>Admin</h1>';

        $aff->admincss($config, $app);
        $aff->adminpassword($config);
        $aff->admindb($config);
        if ($app->setbdd($config)) {
            $status = "OK";
        } else {
            $status = "Not Connected";
        }
        $aff->admintable($config, $status, $app->tablelist($config->dbname()));
        $aff->admindisplay($config->color4());

        echo '</main>';
    } elseif ($_GET['aff'] == 'media') {
        echo '<h1>Media</h1>';
        echo '<main>';
        echo '<article>';

        $aff->addmedia($app);
        $aff->medialist($app->getlistermedia($app::MEDIA_DIR), $app::MEDIA_DIR);

        echo '</article>';
        echo '</main>';

    } elseif ($_GET['aff'] == 'record') {
        echo '<h1>Record</h1>';
        echo '<main>';

        $aff->recordlist($app);

        echo '</main>';

    } elseif ($_GET['aff'] == 'info') {



    } else {
        
    echo '<h1>Private</h1><p>You dont have the permission to access this page.</p>';

    }


} else {
    echo '<h1>Private</h1><p>You should be connected to access this page.</p>';
}

?>