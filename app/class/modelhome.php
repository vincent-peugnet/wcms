<?php

class Modelhome extends Modelart
{
 	
	public function __construct() {
		parent::__construct();
	}

    public function optinit($table)
    {

        $opt = new Opt(Art2::classvarlist());
        $opt->setcol(['id', 'tag', 'linkfrom', 'linkto', 'description', 'title', 'datemodif', 'datecreation', 'date', 'secure', 'visitcount']);
        $opt->settaglist($table);
        $opt->setauthorlist($table);
        $opt->submit();

        return $opt;
    }




    public function table2($table, $opt)
    {
        $listmanager = new Modelart;


        $filtertagfilter = $listmanager->filtertagfilter($table, $opt->tagfilter(), $opt->tagcompare());
        $filterauthorfilter = $listmanager->filterauthorfilter($table, $opt->authorfilter(), $opt->authorcompare());
        $filtersecure = $listmanager->filtersecure($table, $opt->secure());

        $filter = array_intersect($filtertagfilter, $filtersecure, $filterauthorfilter);
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

        $listmanager->artlistsort($table2, $opt->sortby(), $opt->order());


        return $table2;
    }

    /**
     * @param array array of the columns to show from the user
     * 
     * @return array assoc each key columns to a boolean value to show or not
     */
    public function setcolumns(array $columns) : array
    {
        foreach (Model::COLUMNS as $col) {
            if(in_array($col, $columns)) {
                $showcols[$col] = true;
            } else {
                $showcols[$col] = false;
            }
        }
        return $showcols;
    }
}








?>