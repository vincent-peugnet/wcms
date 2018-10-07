<?php


$aff->header();

echo '<section class="home">';


if ($app->session() >= $app::EDITOR) {

    $app->bddinit($config);

    $opt = new Opt(Art::classvarlist());
    $opt->setcol(['id', 'tag', 'lien', 'contenu', 'intro', 'titre', 'datemodif', 'datecreation', 'secure']);
    $table = $app->getlisteropt($opt);
    $app->listcalclien($table);
    $opt->settaglist($table);
    $opt->submit();
    
    
    

   

   

 


    $opt->setcol(['id', 'tag', 'lien', 'contenu', 'intro', 'titre', 'datemodif', 'datecreation', 'secure', 'liento']);


    $aff->option($app, $opt);

    $filtertagfilter = $app->filtertagfilter($table, $opt->tagfilter(), $opt->tagcompare());
    $filtersecure = $app->filtersecure($table, $opt->secure());

    $filter = array_intersect($filtertagfilter, $filtersecure);
    $table2 = [];
    $table2invert = [];
    foreach ($table as $art) {
            if (in_array($art->id(), $filter)) {
                $table2[] = $art;
            } else {
                $table2invert[] = $art;
            }

        
    }

    if(!empty($opt->invert())) {
        $table2 = $table2invert;
    }

    $app->artlistsort($table2, $opt->sortby(), $opt->order());


    echo '<div id="flex">';
    
    
    $aff->home2table($app, $table2, $app->getlister());   




    echo '<div id="map">';
    $aff->mapheader();    
    if(isset($_GET['map'])) {
        $aff->mermaid($app->map($table2));
    }
    echo '</div>';





    echo '</div>';

}



echo '</section>';

?>