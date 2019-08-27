<?php


namespace App\Geo;


class Polygon
{
    /** @var Line[] $lines */
    public $lines = [];

    public $tail = -1;
    public $head = 0;
    public $next = 0;

    public function addLine($line)
    {
        if (count($this->lines) == 0) {
            $this->tail = $line->id;
        } else {
            $this->head = $line->id;
        }
        $this->next = $line->next->id;
        $this->lines[] = $line;
    }

    public function isClosed()
    {
        return ($this->tail == $this->next);
    }

    public function isEmpty()
    {
        return (count($this->lines) == 0);
    }

    public function toWKT()
    {
        return sprintf('POLYGON(%s)', (string) $this);
    }


}
