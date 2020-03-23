<?php

namespace Wcms;

class Modelhome extends Modelpage
{
 	
	public function __construct() {
		parent::__construct();
	}

    public function optinit($table)
    {

        $opt = new Opt(Page::classvarlist());
        $opt->setcol(['id', 'tag', 'linkto', 'description', 'title', 'datemodif', 'datecreation', 'date', 'secure', 'authors', 'visitcount', 'editcount', 'affcount']);
        $opt->settaglist($table);
        $opt->setauthorlist($table);
        $opt->setpageidlist($table);
        $opt->submit();

        return $opt;
    }

    /**
     * Initialise Optlist object using
     * 
     * @param array $table the list of all pages objects
     * 
     * @return Optlist Object initialized
     */
    public function Optlistinit(array $table)
    {
        $optlist = new Optlist(Page::classvarlist());
        $optlist->settaglist($table);
        $optlist->setauthorlist($table);

        return $optlist;
    }




    /**
     * @param array $table
     * @param Opt $opt
     * @param string $regex
     * 
     * @return array of `Page` object
     */
    public function table2(array $table, Opt $opt, string $regex = "", array $searchopt = []) : array
    {


        $filtertagfilter = $this->filtertagfilter($table, $opt->tagfilter(), $opt->tagcompare());
        $filterauthorfilter = $this->filterauthorfilter($table, $opt->authorfilter(), $opt->authorcompare());
        $filtersecure = $this->filtersecure($table, $opt->secure());
        $filterlinkto = $this->filterlinkto($table, $opt->linkto());

        $filter = array_intersect($filtertagfilter, $filtersecure, $filterauthorfilter, $filterlinkto);
        $table2 = [];
        $table2invert = [];
        foreach ($table as $page) {
            if (in_array($page->id(), $filter)) {
                $table2[] = $page;
            } else {
                $table2invert[] = $page;
            }


        }

        if (!empty($opt->invert())) {
            $table2 = $table2invert;
        }

        if(!empty($regex)) {
            $table2 = $this->deepsearch($regex, $searchopt, $table2);
        }

        $this->pagelistsort($table2, $opt->sortby(), $opt->order());

        if($opt->limit() !== 0) {
            $table2 = array_slice($table2, 0, $opt->limit());
        }


        return $table2;
    }


	/**
	 * Search for regex and count occurences
	 * 
	 * @param string $regex Regex to match.
	 * @param array $options Option search, could be `content` `title` `description`.
	 * @param array $page list Array of Pages.
	 * 
	 * @return array associative array of Pages
	 */
	public function deepsearch(string $regex, array $options, array $pagelist) : array
	{
        if($options['casesensitive']) {
            $case = '';
        } else {
            $case = 'i';
        }
        $regex = '/' . $regex . '/' . $case;
        $pageselected = [];
		foreach ($pagelist as $page) {
			$count = 0;
			if($options['content']) {
				$count += preg_match($regex, $page->main());
				$count += preg_match($regex, $page->nav());
				$count += preg_match($regex, $page->aside());
				$count += preg_match($regex, $page->header());
				$count += preg_match($regex, $page->footer());
			}
			if ($options['other']) {
				$count += preg_match($regex, $page->body());
				$count += preg_match($regex, $page->css());
				$count += preg_match($regex, $page->javascript());
			}
			if ($options['title']) {
				$count += preg_match($regex, $page->title());
			}
			if ($options['description']) {
				$count += preg_match($regex, $page->description());
			}
			if ($count !== 0) {
				$pageselected[] = $page;
			}
		}
		return $pageselected;
    }
    
    /**
     * Transform list of page into list of nodes and edges
     */
    public function cytodata(array $pagelist, string $layout = 'random')
    {
        $datas['elements'] = $this->mapdata($pagelist);

        $datas['layout'] = [
            'name' => $layout,
        ];
        $datas['style'] = [
            [
                'selector' => 'node',
                'style' => [
                    'label' => 'data(id)',
                ],
            ],
            [
                'selector' => 'edge',
                'style' => [
                    'curve-style' => 'bezier',
                    'target-arrow-shape' => 'triangle',
                ],
            ],
        ];
        return $datas;
    }

    public function mapdata(array $pagelist)
    {
        $nodes = [];
        $edges = [];
        foreach ($pagelist as $page) {
            $node['group'] = 'nodes';
            $node['data']['id'] = $page->id();
            $node['classes'] = [$page->secure('string')];
            $nodes[] = $node;


            foreach ($page->linkto() as $linkto) {
                $edge['group'] = 'edges';
                $edge['data']['id'] = $page->id() . '>' . $linkto;
                $edge['data']['source'] = $page->id();
                $edge['data']['target'] = $linkto;
                $edges[] = $edge;
            }
        }
        return array_merge($nodes, $edges);

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