<?php


namespace App\Geo;

use Illuminate\Support\Facades\DB;

class OSM
{
    public static function relationGeometry($relationId)
    {
        $empty_ways = [];
        $open_ways = [];
        $first = null;
        $last = null;

        $multiPolygons = new MultiPolygon($relationId);
        $dbWays = self::getWays($relationId);
        foreach ($dbWays as $dbWay) {
            $way = new Way($dbWay->id, $dbWay->sequence);
            $dbNodes = self::getNodes($dbWay->id);
            if (count($dbNodes) > 0) {
                foreach ($dbNodes as $dbNode) {
                    $way->addNode(new Node($dbNode->id, $dbNode->latitude, $dbNode->longitude, $dbNode->sequence));
                }
            } else {
                $empty_ways[$dbWay->id] = true;
            }
            $isOpen = $multiPolygons->addWay($way);
            if ($isOpen) {
                $open_ways[$dbWay->id] = true;
            }
        }
        $multiPolygons->finishPolygon();

        return [
            "polygons" => $multiPolygons,
            "open_ways" => $open_ways,
            "empty_ways" => $empty_ways
        ];
    }


    private static function getWays($relationId)
    {
        return DB::table('relations')
            ->leftJoin('relation_members', 'relations.id', '=', 'relation_members.relation_id')
            ->where('id', '=', $relationId)
            ->where('member_type', '=', 'way')
            ->orderBy('relation_members.sequence', 'ASC')
            ->get([
                'relation_members.member_id as id',
                'relation_members.member_type as type',
                'relation_members.member_role as role',
                'relation_members.sequence as sequence'
            ]);
    }

    private static function getNodes($wayId)
    {
        return DB::table('way_nodes')
            ->leftJoin('nodes', 'way_nodes.node_id', '=', 'nodes.id')
            ->where('way_nodes.way_id', '=', $wayId)
            ->orderBy('way_nodes.sequence', 'ASC')
            ->get([
                'nodes.id as id',
                'nodes.latitude as latitude',
                'nodes.longitude as longitude',
                'way_nodes.sequence as sequence'
            ]);
    }

    public static function toWKT($geometry)
    {


    }

}
