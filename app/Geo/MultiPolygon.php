<?php

namespace App\Geo;

use Illuminate\Support\Arr;

class MultiPolygon
{

    public $id = null;
    /** @var Way[] $ways */
    public $ways = [];

    /** @var Polygon[] $ways */
    public $polygons = [];
    /** @var Polygon[] $ways */
    public $polygon;

    public $open_ways = [];
    public $empty_ways = [];

    public function __construct($id)
    {
        $this->id = $id;
        $this->polygon = new Polygon();
    }

    /**
     * @param Way $way
     */
    public function addWay($way)
    {
        if ($way->isEmpty()) {
            $this->empty_ways[$way->id] = $way->id;
        } else {
            $this->ways[$way->id] = $way;
        }
    }

    public function finishPolygon()
    {
        /**
         * This is to fix a problem with OSM relations where some ways are out of order
         */
        $non_matched_ways = 0;
        do {
            if ($this->polygon->isEmpty()) {
                $this->addPolygonWay(Arr::first($this->ways));
            } else {
                $link = $this->polygon->getLastWay()->getLastNode()->id;
                $match = $this->getMatchingWayIdFirst($link);
                if ($match['position'] == 'none') {
                    $non_matched_ways++;
                } else {
                    $way = Arr::get($this->ways, $match['way']);
                    if ($match['position'] == 'last') {
                        $way->reverse();
                    }
                    $this->addPolygonWay($way);
                }
            }
        } while (count($this->ways) > $non_matched_ways);

        if ($this->polygon->isClosed()) {
            $this->polygons[] = $this->polygon;
        }
        $this->open_ways = $this->ways;
        unset($this->polygon);
    }

    private function addPolygonWay($way)
    {
        if ($way != null) {
            $this->polygon->addWay($way);
            Arr::forget($this->ways, $way->id);
        }
        if ($this->polygon->isClosed()) {
            $this->polygons[] = $this->polygon;
            $this->polygon = new Polygon();
        }
    }

    private function getMatchingWayIdFirst($node_id)
    {
        $position = 'none';
        $id = null;
        foreach ($this->ways as $index => $way) {

            if ($way->getFirstNode()->id == $node_id) {
                $position = 'first';
                $id = $index;
                break;
            } elseif ($way->getLastNode()->id == $node_id) {
                $position = 'last';
                $id = $index;
                break;
            }
        }
        return [
            'position' => $position,
            'way' => $id,
        ];
    }


}
