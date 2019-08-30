<?php


namespace App\Geo;

class Way
{
    public $id;
    public $sequence;

    /** @var Node[] $nodes */
    public $nodes = [];

    public function __construct($id, $sequence)
    {
        $this->id = $id;
        $this->sequence = $sequence;
    }

    public function addNode($node)
    {
        $this->nodes[] = $node;
    }

    public function isEmpty()
    {
        return count($this->nodes) == 0;
    }

    public function getFirstNode()
    {
        return $this->nodes[0] ?? null;
    }

    public function getLastNode()
    {
        return $this->nodes[count($this->nodes) - 1] ?? null;
    }

    public function reverse()
    {
        $this->nodes = array_reverse($this->nodes);
    }
}
