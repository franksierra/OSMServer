<?php


namespace App\Console\Commands;


use App\Geo\Line;

use App\Models\OSM\RelationTag;
use App\Models\TerritorialDivision;
use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

class OsmAdminLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'osm:admin-levels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compile all the admin levels in the Data Base';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $countryTags = RelationTag::where('k', '=', 'admin_level')
            ->whereIn('v', [2])
            ->orderBy('relation_id', 'ASC')->get();
        foreach ($countryTags as $tag) {

            $tags = $tag->relation->tags()->where('k', '=', 'name')->first();
            $name = $tags->v;
            $geometry = $this->relationGeometry($tag->relation->id);

            TerritorialDivision::create([
                'relation_id' => $tag->relation->id,
                'parent_relation_id' => '0',
                'name' => $name,
//                'geometry' => new MultiPolygon()
            ]);

        }


        return true;
    }

    private function relationGeometry($relationId)
    {
        /** @var Point[] $points */
        $points = [];
        /** @var Line[] $lines */
        $lines = [];
        $empty = [];
        $first = null;
        $last = null;

        $ways = DB::table('relations')
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
        foreach ($ways as $way) {

            $nodes = DB::table('way_nodes')
                ->leftJoin('nodes', 'way_nodes.node_id', '=', 'nodes.id')
                ->where('way_nodes.way_id', '=', $way->id)
                ->orderBy('way_nodes.sequence', 'ASC')
                ->get([
                    'nodes.id as id',
                    'nodes.latitude as latitude',
                    'nodes.longitude as longitude',
                    'way_nodes.sequence as sequence'
                ]);
            if ($nodes->count() > 0) {
                foreach ($nodes as $node) {
                    $points[] = new Point($node->latitude, $node->latitude);
                }
                $lines[$way->id] = new Line($way->id, $way->sequence, $points);
            } else {
                $empty[$way->id] = true;
            }
            $lines[$way->id]->previous = $last;
            if ($first == null) {
                $first =& $lines[$way->id];
            }
            $last =& $lines[$way->id];
            unset($nodes, $node);
        }

        unset($ways, $way, $nodes, $node);
        $next =& $first;
        foreach (array_reverse($lines) as &$way) {
            $way->next = $next;
            $next =& $way;
        }
        $first->previous =& $last;
        unset($next, $first, $last, $way);

        $multiPolygons = new MultiPolygon($relationId);
        $current = array_values($lines)[0];
        $first = $current;
        while ($current != null) {
            $multiPolygons->addLine($current);
            $next = $current->next;
            if ($next->id != $first->id) {
                $current = $next;
            } else {
                $current = null;
            }
        }
        $multiPolygons->finishPolygon($empty);

        return $multiPolygons;
    }


}
