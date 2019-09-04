<?php


namespace App\Geo;


class Node
{
    public $id;
    public $latitude; // X Lat
    public $longitude; // Y Lon
    public $sequence;

    public function __construct($id, $latitude, $longitude, $sequence)
    {
        $this->id = $id;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->sequence = $sequence;
    }

}

