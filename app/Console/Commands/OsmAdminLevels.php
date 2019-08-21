<?php


namespace App\Console\Commands;


use App\Models\OSM\Relation;
use App\Models\OSM\RelationTag;
use App\Models\TerritorialDivision;
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
        $relation = Relation::->tags->where("k", "=", "admin_level")
            ->where("v", "=", "2");
        dd($relation);
//        foreach ($countries as $country) {
//
//            $tag = $country->tags->where("k", "=", "name")->first();
////            TerritorialDivision::create([
////                'relation_id' => $country->relation_id,
////                'parent_relation_id' => "0",
////                'name' => $tag->v
////            ]);
//
//        }


        return true;
    }


}
