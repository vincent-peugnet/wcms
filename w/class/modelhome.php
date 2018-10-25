<?php

class Modelhome extends Modeldb
{
    public function table2()
    {
        $artmanager = new Modelart;

        $opt = new Opt(Art2::classvarlist());
        $opt->setcol(['id', 'tag', 'linkfrom', 'contenu', 'description', 'title', 'datemodif', 'datecreation', 'secure']);
        $table = $artmanager->getlisteropt($opt);
        $artmanager->listcalclinkfrom($table);
        $opt->settaglist($table);
        $opt->submit();


        $opt->setcol(['id', 'tag', 'linkfrom', 'contenu', 'description', 'title', 'datemodif', 'datecreation', 'secure', 'linkto']);


        //$aff->option($app, $opt);

        echo '<h3>Options</h3>';


        $filtertagfilter = $artmanager->filtertagfilter($table, $opt->tagfilter(), $opt->tagcompare());
        $filtersecure = $artmanager->filtersecure($table, $opt->secure());

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

        if (!empty($opt->invert())) {
            $table2 = $table2invert;
        }

        $artmanager->artlistsort($table2, $opt->sortby(), $opt->order());


        return $table2;
    }
}








?>