<?php


namespace App\Geo;


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
        if (!$this->polygon->isEmpty()) {
            $this->polygons[] = $this->polygon;
        }
        unset($this->polygon);
    }

    public function getGeom()
    {
        $place1->area = new \Grimzy\LaravelMysqlSpatial\Types\Polygon([new \Grimzy\LaravelMysqlSpatial\Types\LineString([
            new \GeoJson\Geometry\Point(40.74894149554006, -73.98615270853043),
            new \Grimzy\LaravelMysqlSpatial\Types\Point(40.74848633046773, -73.98648262023926),
            new Point(40.747925497790725, -73.9851602911949),
            new Point(40.74837050671544, -73.98482501506805),
            new Point(40.74894149554006, -73.98615270853043)
        ])]);


        $geomString = '';

    }
}
