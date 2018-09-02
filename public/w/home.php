<?php


$aff->header();

echo '<section class="home">';


if ($app->session() >= $app::EDITOR) {

    $app->bddinit($config);
    $opt = new Opt(Art::classvarlist());

    if(!empty($_GET)) {
    
            $_SESSION['opt'] = $_GET;
            $opt->hydrate($_GET);

    }
    if(isset($_GET['submit']) && $_GET['submit'] == 'reset') {
        $opt = new Opt(Art::classvarlist());
    } else {
        $opt->hydrate($_SESSION['opt']);

    }

    $opt->setcol(['id', 'tag', 'lien', 'contenu', 'intro', 'titre', 'datemodif', 'datecreation', 'secure']);
    $table = $app->getlisteropt($opt);
    $app->listcalclien($table);
    $opt->settaglist($table);
    $opt->setcol(['id', 'tag', 'lien', 'contenu', 'intro', 'titre', 'datemodif', 'datecreation', 'secure', 'liento']);

    $aff->option($app, $opt);

    $filtertagfilter = $app->filtertagfilter($table, $opt->tagfilter(), $opt->tagcompare());
    $filtersecure = $app->filtersecure($table, $opt->secure());

    $filter = array_intersect($filtertagfilter, $filtersecure);
    $table2 = [];
    foreach ($table as $art) {
        if (in_array($art->id(), $filter)) {
            $table2[] = $art;
        }
    }

    $app->artlistsort($table2, $opt->sortby(), $opt->order());



    $aff->home2table($app, $table2);
}



echo '</section>';

?>