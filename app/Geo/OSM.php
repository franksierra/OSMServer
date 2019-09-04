<?php


namespace App\Geo;

use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Facades\DB;

class OSM
{
    public static function relationGeometry($relationId)
    {
        $multiPolygons = new MultiPolygon($relationId);
        $dbWays = self::getWays($relationId);
        foreach ($dbWays as $dbWay) {
            $way = new Way($dbWay->id, $dbWay->sequence);
            $dbNodes = self::getNodes($dbWay->id);
            foreach ($dbNodes as $dbNode) {
                $way->addNode(new Node($dbNode->id, $dbNode->latitude, $dbNode->longitude, $dbNode->sequence));
            }
            $multiPolygons->addWay($way);
        }
        $multiPolygons->finishPolygon();

        return $multiPolygons;
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
            ->orderBy('way_nodes.sequence', 'DESC')
            ->get([
                'nodes.id as id',
                'nodes.latitude as latitude',
                'nodes.longitude as longitude',
                'way_nodes.sequence as sequence'
            ]);
    }

    public static function toSpatial($geometry)
    {
        // MULTIPOLYGON
        //      POLYGON
        //          POINT
        $polygons = [];
        foreach ($geometry->polygons as $polygon) {
            $linestring = [];
            /** @var Way $way */
            $points = [];
            foreach ($polygon->ways as $way) {
                /** @var Node $node */
                foreach ($way->nodes as $node) {
                    $points[] = new Point($node->latitude, $node->longitude);
                }
            }
            $linestring[] = new LineString($points);
            $polygons[] = new \Grimzy\LaravelMysqlSpatial\Types\Polygon($linestring);
        }
        return new \Grimzy\LaravelMysqlSpatial\Types\MultiPolygon($polygons);

    }

}
