<?php

namespace Wcms;

class Modelhome extends Model
{
    public const GRAPH_LAYOUTS = [
        'cose' => 'cose',
        'cose-bilkent' => 'cose-bilkent',
        'circle' => 'circle',
        'breadthfirst' => 'breadthfirst',
        'concentric' => 'concentric',
        'grid' => 'grid',
        'random' => 'random',
    ];

    /**
     * Transform list of page into list of nodes and edges
     *
     * @param Page[] $pagelist associative array of pages as `id => Page`
     * @param string $layout
     * @param bool $showorphans if `false`, remove orphans pages
     * @param bool $showredirection if `true`, add redirections
     *
     * @return array[]
     */
    public function cytodata(
        array $pagelist,
        string $layout = 'random',
        bool $showorphans = false,
        bool $showredirection = false
    ): array {
        $datas['elements'] = $this->mapdata($pagelist, $showorphans, $showredirection);

        $datas['layout'] = [
            'name' => $layout,
            'quality' => 'proof',
            'fit' => true,
            'randomize' => true,
            'nodeDimensionsIncludeLabels' => true,
            'tile' => false,
            'edgeElasticity' => 0.45,
            'gravity' => 0.25,
            'idealEdgeLength' => 60,
            'numIter' => 10000
        ];
        $datas['style'] = [
            [
                'selector' => 'node',
                'style' => [
                    'label' => 'data(id)',
                    'background-image' => 'data(favicon)',
                    'background-fit' => 'contain',
                    'border-width' => 3,
                    'border-color' => '#80b97b'
                ],
            ],
            [
                'selector' => 'node.not_published',
                'style' => [
                    'shape' => 'round-hexagon',
                    'border-color' => '#b97b7b'
                ],
            ],
            [
                'selector' => 'node.private',
                'style' => [
                    'shape' => 'round-triangle',
                    'border-color' => '#b9b67b'
                ],
            ],
            [
                'selector' => 'edge',
                'style' => [
                    'curve-style' => 'bezier',
                    'target-arrow-shape' => 'triangle',
                    'arrow-scale' => 1.5
                ],
            ],
            [
                'selector' => 'edge.bidirectionnal',
                'style' => [
                    'curve-style' => 'bezier',
                    'target-arrow-shape' => 'triangle',
                    'source-arrow-shape' => 'triangle',
                    'arrow-scale' => 1.5
                ],
            ],
            [
                'selector' => 'edge.redirect',
                'style' => [
                    'line-style' => 'dashed',
                    'label' => 'data(refresh)'
                ],
            ],
        ];
        return $datas;
    }

    /**
     * Transform list of Pages into cytoscape nodes and edge datas
     *
     * @param Page[] $pagelist associative array of pages as `id => Page`
     * @param bool $showorphans if `false`, remove orphans pages
     * @param bool $showredirection if `true`, add redirections
     *
     * @return array[] of cytoscape datas
     */
    public function mapdata(array $pagelist, bool $showorphans = true, bool $showredirection = false): array
    {
        $idlist = array_keys($pagelist);

        $edges = [];
        $notorphans = [];
        foreach ($pagelist as $page) {
            foreach ($page->linkto() as $linkto) {
                if (in_array($linkto, $idlist)) {
                    if (in_array($page->id(), $pagelist[$linkto]->linkto())) {
                        if ($page->id() > $linkto) {
                        // We have a bi-directionnal link !
                            $edgeb['group'] = 'edges';
                            $edgeb['data']['id'] = $page->id() . '<>' . $linkto;
                            $edgeb['data']['source'] = $page->id();
                            $edgeb['data']['target'] = $linkto;
                            $edgeb['classes'] = 'bidirectionnal';
                            $edges[] = $edgeb;
                            $notorphans[] = $linkto;
                            $notorphans[] = $page->id();
                        }
                    } else {
                        $edge['group'] = 'edges';
                        $edge['data']['id'] = $page->id() . '>' . $linkto;
                        $edge['data']['source'] = $page->id();
                        $edge['data']['target'] = $linkto;
                        $edges[] = $edge;
                        $notorphans[] = $linkto;
                        $notorphans[] = $page->id();
                    }
                }
            }
            // add redirection edge
            if ($showredirection && key_exists($page->redirection(), $pagelist)) {
                $edger['group'] = 'edges';
                $edger['data']['id'] = $page->id() . '>' . $page->redirection();
                $edger['data']['refresh'] = $page->refresh();
                $edger['data']['source'] = $page->id();
                $edger['data']['target'] = $page->redirection();
                $edger['classes'] = 'redirect';
                $edges[] = $edger;
                $notorphans[] = $page->redirection();
                $notorphans[] = $page->id();
            }
        }

        $notorphans = array_unique($notorphans);

        $nodes = [];
        foreach ($pagelist as $id => $page) {
            if ($showorphans || in_array($id, $notorphans)) {
                $node['group'] = 'nodes';
                $node['data']['id'] = $page->id();
                $node['data']['edit'] = $page->id() . DIRECTORY_SEPARATOR . 'edit';
                $node['data']['favicon'] = Model::faviconpath() . $page->favicon();
                $node['classes'] = [$page->secure('string')];
                $nodes[] = $node;
            }
        }

        return array_merge($nodes, $edges);
    }

    /**
     * @param Bookmark[] $bookmarks     List of bookmarks objects
     * @param string $query             Query address to compare
     * @return Bookmark[]               List of all bookmarks that match query
     */
    public function matchedbookmarks(array $bookmarks, string $query): array
    {
        return array_filter($bookmarks, function (Bookmark $bookmark) use ($query) {
            return $bookmark->query() === $query;
        });
    }
}
