<?php

namespace Wcms;

use AltoRouter;
use Exception;
use LogicException;

class Optmap extends Optcode
{
    /**
     * Get the code to insert directly
     */
    public function getcode(): string
    {
        return '%MAP' . $this->getquery() . '%';
    }

    /**
     * Generate the HTML code for the map
     */
    public function maphtml(array $pages, AltoRouter $router): string
    {
        $geopages = array_map(function (Page $page) use ($router) {
            $data = $page->drylist(['id', 'title', 'latitude', 'longitude']);
            try {
                $data['read'] = $router->generate('pageread', ['page' => $page->id()]);
            } catch (Exception $e) {
                throw new LogicException($e->getMessage());
            }
            return $data;
        }, $pages);
        $geopages = array_values($geopages);
        $json = json_encode($geopages);
        $mapid = 'geomap';

        $html = "<div id=\"$mapid\" class=\"map\" style=\"min-height: 400px; min-width: 400px;\"></div>\n";

        $html .= "<script>var pages = $json;\nvar mapId = \"$mapid\";\n</script>";



        return $html;
    }
}
