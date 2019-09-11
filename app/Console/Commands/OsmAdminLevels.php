<?php


namespace App\Console\Commands;

use App\Geo\OSM;
use App\Models\OSM\RelationTag;
use App\Models\TerritorialDivision;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
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
        Config::set('app.debug', false);

        DB::table('territorial_divisions')->truncate();
        $territories = RelationTag::where('k', '=', 'admin_level')
            ->orderBy('relation_id', 'ASC')->get();
        foreach ($territories as $territory) {
            $level = $territory->v;
            $tags = $territory->relation->tags()->where('k', '=', 'name')->first();
            $name = $tags->v;
            $geometry = OSM::relationGeometry($territory->relation->id);
            if ($geometry != null && count($geometry->empty_ways) == 0 && count($geometry->polygons) > 0) {
                $multiPolygon = OSM::toSpatial($geometry);
                TerritorialDivision::create([
                    'relation_id' => $tags->relation->id,
                    'parent_relation_id' => '-1',
                    'name' => $name,
                    'admin_level' => $level,
                    'geometry' => $multiPolygon
                ]);
            }
        }

        TerritorialDivision::where('admin_level', '=', 2)->update(['parent_relation_id' => 0]);
        $admin_levels = TerritorialDivision::groupBy('admin_level')
            ->orderBy('admin_level')
            ->get('admin_level')
            ->pluck('admin_level');
        foreach ($admin_levels as $index => $admin_level) {
            $parent_zones = TerritorialDivision::where('admin_level', '=', $admin_level)->get();
            foreach ($parent_zones as $parent_zone) {
                $admin_level_index = $index;
                $child_zones = [];
                while (count($child_zones) == 0 && $admin_level_index < (count($admin_levels) - 1)) {
                    $admin_level_index++;
                    $child_zones = TerritorialDivision::within('geometry', $parent_zone->geometry)
                        ->where('admin_level', '=', $admin_levels[$admin_level_index])
                        ->get('relation_id');
                };
                foreach ($child_zones as $child_zone) {
                    $child_zone->parent_relation_id = $parent_zone->relation_id;
                    $child_zone->save();
                }
            }
        }

        return true;
    }


}
