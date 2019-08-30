<?php


namespace App\Geo;


class Polygon
{
    /** @var Way[] $ways */
    public $ways = [];

    /**
     * @param Way $way
     */
    public function addWay($way)
    {
        $this->ways[] = $way;
    }

    public function isClosed()
    {
        if ($this->getFirstWay() != null && $this->getLastWay() != null) {
            return $this->getFirstWay()->getFirstNode()->id == $this->getLastWay()->getLastNode()->id;
        }
        return false;
    }

    public function isEmpty()
    {
        return (count($this->ways) == 0);
    }

    public function getFirstWay()
    {
        return $this->ways[0] ?? null;
    }

    public function getLastWay()
    {
        return $this->ways[count($this->ways) - 1] ?? null;
    }

    public function reverse()
    {
        $this->ways = array_reverse($this->ways);
        /** @param Way $value */
        $func = function ($value) {
            $value->reverse();
        };
        array_map($func, $this->ways);
    }
}
