<?php


namespace App\Console\Commands;

use App\Geo\OSM;
use App\Models\OSM\Relation;
use App\Models\OSM\RelationTag;
use App\Models\TerritorialDivision;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Phaza\LaravelPostgis\Geometries\Point;

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
        DB::table('territorial_divisions')->truncate();
        $territories = RelationTag::where('k', '=', 'admin_level')
            ->orderBy('relation_id', 'ASC')->get();

        $this->output->progressStart(count($territories));
        foreach ($territories as $territory) {
            $level = $territory->v;
            $tags = $territory->relation->tags()->where('k', '=', 'name')->first();
            $name = $tags->v ?? '';
            $geometry = OSM::relationGeometry($territory->relation->id);
            if ($geometry != null && count($geometry->empty_ways) == 0 && count($geometry->polygons) > 0) {
                $multiPolygon = OSM::toSpatial($geometry);
                TerritorialDivision::create([
                    'relation_id' => $territory->relation->id,
                    'parent_relation_id' => '-1',
                    'name' => $name,
                    'admin_level' => $level,
                    'geometry' => $multiPolygon
                ]);
            }
            $this->output->progressAdvance(1);
        }
        $this->output->progressFinish();

        TerritorialDivision::where('admin_level', '=', 2)->update(
            [
                'parent_relation_id' => 0,
            ]
        );
        $admin_levels = TerritorialDivision::groupBy('admin_level')
            ->orderBy('admin_level')
            ->get('admin_level')
            ->pluck('admin_level');
        $this->output->progressStart(TerritorialDivision::count('relation_id'));
        foreach ($admin_levels as $index => $admin_level) {
            $parent_zones = TerritorialDivision::where('admin_level', '=', $admin_level)->get('relation_id');
            foreach ($parent_zones as $parent_zone) {
                $child_zones = TerritorialDivision::whereRaw(
                    "ST_Within(geometry, (SELECT geometry FROM territorial_divisions WHERE relation_id = $parent_zone->relation_id))"
                )
                    ->where('admin_level', '>', $admin_level)
                    ->get('relation_id');
                foreach ($child_zones as $child_zone) {
                    $child_zone->parent_relation_id = $parent_zone->relation_id;
                    $child_zone->save();
                    $this->output->progressAdvance(1);
                }
            }
        }
        $this->output->progressFinish();

//        /// Dumps a list...
//        file_put_contents(
//            'text.txt',
//            "INSERT INTO divisiones_territoriales (id,id_division_territorial,id_osm,nombre,nivel,latitud,longitud,admin_level,id_usuario_creacion) VALUES\n"
//        );
//        $this->pretyPrint();
        return true;
    }

    private function pretyPrint($parent_zone_id = 0, $dept = 0, $last_str = '')
    {
        $p_id = $this->equivalentId($parent_zone_id);
        $zones = TerritorialDivision::where('parent_relation_id', '=', $parent_zone_id);
        if ($parent_zone_id === 0) {
            $countries = [108089, 114686, 120027, 1520612, 1521463, 167454, 287666, 287667, 287668, 287670, 288247, 307828];
            $zones->whereIn('relation_id', $countries);
        }
        $zones = $zones
            ->orderBy('name')
            ->get([
                'relation_id',
                'parent_relation_id',
                'name',
                'admin_level'
            ]);
        foreach ($zones as $index => $zone) {
            $relation = Relation::find($zone->relation_id);
            $label = $relation->nodes()->where('member_role', '=', 'label')->first();
            if (!$label) {
                $label = $relation->nodes()->where('member_role', '=', 'admin_centre')->first();
                if (!$label) {
                    $label = TerritorialDivision::where('relation_id', '=', $zone->relation_id)
                        ->selectRaw('ST_Centroid(geometry) as point, "" as latitude, "" as longitude')
                        ->first();
                    $point = Point::fromWKB($label->point);
                    $label->latitude = $point->getLat();
                    $label->longitude = $point->getLng();
                }
            }
            $id = $this->equivalentId($zone->relation_id);
            $zone->name = addslashes($zone->name);
            $str = "({$id},{$p_id},{$zone->relation_id},'{$zone->name}',{$dept},'{$label->latitude}','{$label->longitude}',{$zone->admin_level},1),";
            file_put_contents('text.txt', $str . "\n", FILE_APPEND);
            $dept++;
            $this->pretyPrint($zone->relation_id, $dept, $str);
            $dept--;
        }
    }


    protected $relations = [];

    private function equivalentId($relation_id)
    {
        if (!isset($this->relations[$relation_id])) {
            $this->relations[$relation_id] = count($this->relations);
        }
        return $this->relations[$relation_id];
    }


}
