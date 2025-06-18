<?php

namespace Wcms;

class Modelhome extends Model
{
    /**
     * Transform list of page into list of nodes and edges
     *
     * @param Page[] $pagelist              associative array of pages as `id => Page`
     * @param Graph $graph                  Graph settings
     *
     * @return mixed[]
     */
    public function cytodata(array $pagelist, Graph $graph): array
    {
        $datas['elements'] = $this->mapdata(
            $pagelist,
            $graph->showorphans(),
            $graph->showredirection(),
            $graph->showexternallinks()
        );

        $datas['wheelSensitivity'] = 0.1;
        $datas['layout'] = [
            'name' => $graph->layout(),
            'quality' => 'proof',
            // 'fit' => true,
            'randomize' => true,
            'nodeDimensionsIncludeLabels' => true,
            'tile' => false,
            // 'edgeElasticity' => 0.45,
            // 'gravity' => 0.25,
            // 'idealEdgeLength' => 60,
            // 'numIter' => 10000,
        ];
        $datas['style'] = [
            [
                'selector' => 'node',
                'style' => [
                    'label' => 'data(name)',
                    'background-image' => 'data(favicon)',
                    'background-fit' => 'contain',
                    // 'border-width' => 3,
                    'background-color' => '#80b97b',
                    'text-valign' => 'center',
                    'font-size' => 12,
                    'width' => 'data(size)',
                    'height' => 'data(size)',
                ],
            ],
            [
                'selector' => 'node.not_published',
                'style' => [
                    'shape' => 'round-hexagon',
                    'background-color' => '#b97b7b'
                ],
            ],
            [
                'selector' => 'node.private',
                'style' => [
                    'shape' => 'round-triangle',
                    'background-color' => '#b9b67b'
                ],
            ],
            [
                'selector' => 'node.domain',
                'style' => [
                    'shape' => 'round-diamond',
                    'border-width' => 0,
                    'width' => 16,
                    'height' => 16,
                    'text-opacity' => 0.75,
                    'font-size' => 8,
                ],
            ],
            [
                'selector' => 'node.domain.ok',
                'style' => [
                    'background-color' => '#80b97b',
                ],
            ],
            [
                'selector' => 'node.domain.dead',
                'style' => [
                    'background-color' => '#b97b7b',
                ],
            ],
            [
                'selector' => 'edge',
                'style' => [
                    'curve-style' => 'straight',
                    'width' => 2,
                    'target-arrow-shape' => 'triangle',
                    'arrow-scale' => 1.3
                ],
            ],
            [
                'selector' => 'edge.bidirectionnal',
                'style' => [
                    'source-arrow-shape' => 'triangle',
                ],
            ],
            [
                'selector' => 'edge.redirect',
                'style' => [
                    'line-style' => 'dashed',
                    'label' => 'data(refresh)'
                ],
            ],
            [
                'selector' => 'edge.url',
                'style' => [
                    'width' => 1,
                    'arrow-scale' => 0.7,
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
     * @return array<int<0, max>, array<string, array<int|string, mixed>|string>> of cytoscape datas
     */
    public function mapdata(
        array $pagelist,
        bool $showorphans = true,
        bool $showredirection = false,
        bool $showexternallinks = false
    ): array {
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
                $node['data']['name'] = empty($page->title()) ? $page->id() : $page->title();
                $node['data']['leftclick'] = $page->id();
                $node['data']['size'] = 35; // Size of page node
                $node['data']['edit'] = $page->id() . DIRECTORY_SEPARATOR . 'edit';
                $node['data']['favicon'] = Model::faviconpath() . $page->favicon();
                $node['classes'] = ['page', $page->secure('string')];
                $nodes[] = $node;

                // external links
                if ($showexternallinks) {
                    foreach ($page->externallinks() as $url => $ok) {
                        $domain = parse_url($url, PHP_URL_HOST);


                        $noded['group'] = 'nodes';
                        $noded['data']['id'] = $url;
                        $noded['data']['name'] = $domain;
                        $noded['data']['leftclick'] = "$url";
                        $noded['classes'] = ['domain', $ok ? 'ok' : 'dead'];
                        $nodes[] = $noded;

                        $edged['group'] = 'edges';
                        $edged['data']['id'] = $page->id() . '>' . $url;
                        $edged['data']['source'] = $page->id();
                        $edged['data']['target'] = $url;
                        $edged['classes'] = 'url';
                        $edges[] = $edged;
                    }
                }
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
