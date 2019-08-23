<?php


namespace App\Geo;


use App\Geo\Polygon;

class MultiPolygon
{
    public $id = null;
    public $polygons = [];
    public $polygon;
    public $empty;

    public function __construct($id)
    {
        $this->id = $id;
        $this->polygon = new Polygon();
    }

    public function addLine($line)
    {
        if (!$this->polygon->isClosed()) {
            $this->polygon->addLine($line);
        }
        if ($this->polygon->isClosed()) {
            $this->polygons[] = $this->polygon;
            $this->polygon = new Polygon();
        }
    }

    public function finishPolygon($empty)
    {
        $this->empty = $empty;
        $this->polygons[] = $this->polygon;
        unset($this->polygon);
    }

    public function getGeom()
    {
        $geomString = '';

    }
}
