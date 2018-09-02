<?php


if ($app->session() >= $app::EDITOR) {

    if ($_GET['aff'] == 'admin' && $app->session() >= $app::ADMIN) {
        echo '<section>';
        echo '<h1>Admin</h1>';

        $aff->admincss($config, $app);
        $aff->adminpassword($config);
        $aff->admindb($config);
        if ($app->setbdd($config)) {
        //var_dump($app->tablelist($config->dbname()));
            echo '<p>database status : OK</p>';
        }
        $aff->admintable($config, $app->tablelist($config->dbname()));

        echo '</section>';
    } elseif ($_GET['aff'] == 'media') {
        echo '<h1>Media</h1>';
        echo '<section>';

        $aff->addmedia($app);
        $aff->medialist($app);

        echo '</section>';

    } elseif ($_GET['aff'] == 'record') {
        echo '<h1>Record</h1>';
        echo '<section>';

        $aff->recordlist($app);

        echo '</section>';

    } elseif ($_GET['aff'] == 'map') {
        $app->bddinit($config);
        $aff->map($app, $config->domain());
    } else {
    echo '<h1>Private</h1><p>You dont have the permission to access this page.</p>';

    }


} else {
    echo '<h1>Private</h1><p>You should be connected to access this page.</p>';
}

?>