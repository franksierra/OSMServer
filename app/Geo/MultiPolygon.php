<?php

namespace App\Geo;

class MultiPolygon
{
    public $id = null;
    public $polygons = [];
    public $polygon;

    public function __construct($id)
    {
        $this->id = $id;
        $this->polygon = new Polygon();
    }

    /**
     * @param Way $way
     * @return bool
     */
    public function addWay($way)
    {
        $open_way = false;
        if (!$this->polygon->isClosed()) {
            if (!$way->isEmpty()) {
                if ($this->polygon->isEmpty()) {
                    $this->polygon->addWay($way);
                } else {
                    if ($this->polygon->getLastWay()->getLastNode()->id == $way->getFirstNode()->id) {
                        // OK
                    } elseif ($this->polygon->getLastWay()->getLastNode()->id == $way->getLastNode()->id) {
                        $way->reverse();
                    } elseif ($this->polygon->getFirstWay()->getFirstNode()->id == $way->getLastNode()->id) {
                        $this->polygon->reverse();
                        $way->reverse();
                    } else {
                        $open_way = true;
                    }
                    $this->polygon->addWay($way);
                }
            }
        }
        if ($this->polygon->isClosed()) {
            $this->polygons[] = $this->polygon;
            $this->polygon = new Polygon();
        }
        return $open_way;
    }

    public function finishPolygon()
    {
        if (!$this->polygon->isEmpty()) {
            $this->polygons[] = $this->polygon;
        }
        unset($this->polygon);
    }
}
