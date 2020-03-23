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
     * Filter the pages list acording to the options and invert
     * 
     * @param array $pagelist of `Page` objects
     * @param Opt $opt
     * 
     * @return array of `string` pages id
     */

    public function filter(array $pagelist, Opt $opt) : array
    {

        $filtertagfilter = $this->filtertagfilter($pagelist, $opt->tagfilter(), $opt->tagcompare());
        $filterauthorfilter = $this->filterauthorfilter($pagelist, $opt->authorfilter(), $opt->authorcompare());
        $filtersecure = $this->filtersecure($pagelist, $opt->secure());
        $filterlinkto = $this->filterlinkto($pagelist, $opt->linkto());

        $filter = array_intersect($filtertagfilter, $filtersecure, $filterauthorfilter, $filterlinkto);

        if($opt->invert()) {
            $idlist = array_keys($pagelist);
            $filter = array_diff($idlist, $filter);
        }

        return $filter;
    }



    /**
     * Convert list of id into a list of Page objects
     * 
     * @param array $pagelist
     * @param array $idlist
     * 
     * @return array Filtered list of `Page` objects
     */
    public function pagelistfilter(array $pagelist, array $fiter) : array
    {
        return array_intersect_key($pagelist, array_flip($fiter));
    }





    /**
     * Sort and limit an array of Pages
     * 
     * @param array $pagelist of `Page` objects
     * @param Opt $opt
     * 
	 * @return array associative array of `Page` objects
     */
    public function sort(array $pagelist, Opt $opt) : array
    {
        $this->pagelistsort($pagelist, $opt->sortby(), $opt->order());

        if($opt->limit() !== 0) {
            $pagelist = array_slice($pagelist, 0, $opt->limit());
        }

        return $pagelist;
    }


	/**
	 * Search for regex and count occurences
	 * 
	 * @param array $page list Array of Pages.
	 * @param string $regex Regex to match.
	 * @param array $options Option search, could be `content` `title` `description`.
	 * 
	 * @return array associative array of `Page` objects
	 */
	public function deepsearch(array $pagelist, string $regex, array $options) : array
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
				$pageselected[$page->id()] = $page;
			}
		}
		return $pageselected;
    }


    
    /**
     * Transform list of page into list of nodes and edges
     * 
     * @param array $pagelist associative array of pages as `id => Page`
     * @param string $layout 
     * @param bool $hideorphans if `true`, remove orphans pages
     * 
     * 
     */
    public function cytodata(array $pagelist, string $layout = 'random', bool $hideorphans = false)
    {
        $datas['elements'] = $this->mapdata($pagelist, $hideorphans);

        $datas['layout'] = [
            'name' => $layout,
            'quality' => 'proof',
            'fit' => true,
            'randomize' => true,
            'nodeDimensionsIncludeLabels' => true,
            'tile' => false,
            'edgeElasticity' => 0.75,
            'gravity' => 0.25,
            'idealEdgeLength' => 60,
            'numIter' => 10000
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

    /**
     * Transform list of Pages into cytoscape nodes and edge datas
     * 
     * @param array $pagelist associative array of pages as `id => Page`
     * @param bool $hideorphans if `true`, remove orphans pages
     * 
     * @return array of cytoscape datas
     */
    public function mapdata(array $pagelist, bool $hideorphans = false) : array
    {
        $idlist = array_keys($pagelist);

        $edges = [];
        foreach ($pagelist as $page) {
            foreach ($page->linkto() as $linkto) {
                if(in_array($linkto, $idlist)) {
                    $edge['group'] = 'edges';
                    $edge['data']['id'] = $page->id() . '>' . $linkto;
                    $edge['data']['source'] = $page->id();
                    $edge['data']['target'] = $linkto;
                    $edges[] = $edge;
                    $notorphans[] = $linkto;
                }
            }
            if(!empty($page->linkto())) {
                $notorphans[] = $page->id();
            }
        }

        $notorphans = array_unique($notorphans);

        $nodes = [];
        foreach ($pagelist as $id => $page) {
            if($hideorphans) {
                if(in_array($id, $notorphans)) {
                    $node['group'] = 'nodes';
                    $node['data']['id'] = $page->id();
                    $node['classes'] = [$page->secure('string')];
                    $nodes[] = $node;
                }
            } else {
                $node['group'] = 'nodes';
                $node['data']['id'] = $page->id();
                $node['classes'] = [$page->secure('string')];
                $nodes[] = $node;
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