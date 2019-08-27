<?php


namespace App\Geo;

use Grimzy\LaravelMysqlSpatial\Types\LineString;

class Line extends LineString
{
    public $id;
    public $sequence;

    public $previous = null;
    public $next = null;

    public function __construct($id, $sequence, $points)
    {
        $this->id = $id;
        $this->sequence = $sequence;
        parent::__construct($points);
    }
}
