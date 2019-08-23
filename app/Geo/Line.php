<?php


namespace App\Geo;

class Line
{
    public $id;
    public $sequence;
    /** @var Point[] $points */
    public $points = [];

    public $previous = null;
    public $next = null;

    public function __construct($id, $sequence)
    {
        $this->id = $id;
        $this->sequence = $sequence;
    }

    public function addPoint(&$point)
    {
        $this->points[] = $point;
    }

}
