<?php

namespace App\Geo;

class Point
{
    public $id;
    public $latitude;
    public $longitude;
    public $sequence;

    public function __construct($id, $latitude, $longitude, $sequence)
    {
        $this->id = $id;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->sequence = $sequence;
    }
}
