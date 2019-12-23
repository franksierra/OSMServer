<?php


namespace App\Geo;

use Illuminate\Support\Facades\DB;
use Phaza\LaravelPostgis\Geometries\LineString;
use Phaza\LaravelPostgis\Geometries\Point;

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
            $polygons[] = new \Phaza\LaravelPostgis\Geometries\Polygon($linestring);
        }
        return new \Phaza\LaravelPostgis\Geometries\MultiPolygon($polygons);

    }

    public static function processElements($elements)
    {
        $type = $elements['type'];
        $tags = [];
        $nodes = [];
        $relations = [];

        foreach ($elements['data'] as &$element) {
            if (isset($element["timestamp"])) {
                $element["timestamp"] = str_replace("T", " ", $element["timestamp"]);
                $element["timestamp"] = str_replace("Z", "", $element["timestamp"]);
            }
            foreach ($element["tags"] as $tag) {
                $tags[] = [
                    $type . "_id" => $element["id"],
                    "k" => $tag["key"],
                    "v" => $tag["value"]
                ];
            }
            foreach ($element["nodes"] as $node) {
                $nodes[] = [
                    $type . "_id" => $element["id"],
                    "node_id" => $node["id"],
                    "sequence" => $node["sequence"]
                ];
            }

            foreach ($element["relations"] as $relation) {
                $relations[] = [
                    $type . "_id" => $element["id"],
                    "member_type" => $relation["member_type"],
                    "member_id" => $relation["member_id"],
                    "member_role" => $relation["member_role"],
                    "sequence" => $relation["sequence"]
                ];
            }
            unset($element["tags"], $element["nodes"], $element["relations"]);
        }

        return [
            'type' => $elements['type'],
            'records' => $elements['data'],
            'tags' => $tags,
            'nodes' => $nodes,
            'relations' => $relations,
        ];

    }

    public static function getQuery($table_name, $values)
    {
        $table = DB::table($table_name);
        if (!is_array(reset($values))) {
            $values = [$values];
        }
        $columns = '(' . $table->getGrammar()->columnize(array_keys(reset($values))) . ')';
        $parameters = collect($values)->map(function ($record) use ($table) {
            $record = implode(', ', array_map(function ($item) {
                return "E'" . addslashes($item) . "'";
            }, $record));
            return '(' . $record . ')';
        })->implode(', ');
        return "insert into {$table_name} {$columns} values {$parameters} on conflict do nothing ;";
    }

    public static function convert($wkt_str)
    {
        $ret_arr = array();
        $matches = array();
        preg_match('/\)\s*,\s*\(/', $wkt_str, $matches);
        if (empty($matches)) {
            $polys = array(
                trim($wkt_str)
            );
        } else {
            $polys = explode($matches[0], trim($wkt_str));
        }
        foreach ($polys as $poly) {
            $ret_arr[] = str_replace('(', '', str_replace(')', '', substr($poly, stripos($poly, '(') + 2, stripos($poly, ')') - 2)));
        }
        $ret_arr = str_replace(' ', ',', $ret_arr);
        return $ret_arr;
    }
}
