<?php


namespace App\Console\Commands;


use App\Geo\MultiPolygon;
use App\Models\OSM\Relation;
use App\Models\OSM\RelationMember;
use App\Models\OSM\RelationTag;
use App\Models\OSM\Way;
use App\Models\OSM\WayNode;
use App\Models\TerritorialDivision;
use Illuminate\Console\Command;

use App\Geo\Point;
use App\Geo\Line;

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

//            TerritorialDivision::create([
//                'relation_id' => $relationId,
//                'parent_relation_id' => '0',
//                'name' => $name,
////                'geometry' => new MultiPolygon()
//            ]);
//
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

//        $ways = RelationMember::leftJoin('ways', 'member_id', '=', 'ways.id')
//            ->where('relation_id', '=', $relationId)
//            ->where('member_type', '=', 'way')
//            ->orderBy('sequence', 'ASC')
//            ->get();
        $relations = Relation::find($relationId)->get();
        $members = $relations->members;

        $ways = RelationMember::find($relationId)->member;
        foreach ($ways as $way) {
            $lines[$way->id] = new Line($way->id, $way->sequence);
            $lines[$way->id]->previous = $last;
            $nodes = $way->nodes()->toSql();
            if ($nodes->count() > 0) {
                foreach ($nodes as $node) {
                    $points[$node->id] = new Point(
                        $node->id,
                        $node->latitude,
                        $node->latitude,
                        $node->sequence
                    );
                    $lines[$way->id]->addPoint($points[$node->id]);
                }
            } else {
                $empty[$way->id] = true;
            }
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

        if (count($empty) > 0) {
            $o = 0;
        }
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
