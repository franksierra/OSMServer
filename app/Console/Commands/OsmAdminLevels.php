<?php


namespace App\Console\Commands;

use App\Geo\OSM;
use App\Models\OSM\RelationTag;
use Illuminate\Console\Command;

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
        $adminLevels = RelationTag::where('k', '=', 'admin_level')
            ->whereIn('v', [2])
            ->orderBy('relation_id', 'ASC')->get();
        foreach ($adminLevels as $adminLevel) {
            $tags = $adminLevel->relation->tags()->where('k', '=', 'name')->first();
            $name = $tags->v;
            $geometry = OSM::relationGeometry($adminLevel->relation->id);
            $o = 0;
//            if ($geometry != null) {
//                TerritorialDivision::create([
//                    'relation_id' => $tag->relation->id,
//                    'parent_relation_id' => '0',
//                    'name' => $name,
//                    'geometry' => $geometry
//                ]);
//            }

        }


        return true;
    }


}
