<?php

namespace App\Console\Commands;

use App\Geo\OSM;
use App\Models\OSM\Node;
use App\Models\OSM\NodeTag;
use App\Models\OSM\Relation;
use App\Models\OSM\RelationMember;
use App\Models\OSM\RelationTag;
use App\Models\OSM\Way;
use App\Models\OSM\WayNode;
use App\Models\OSM\WayTag;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\Storage;
use OSMPBF\OSMReader;

class OsmFixMissingWays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'osm:fixways';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports an OSM file into the database';

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
            $geometry = OSM::relationGeometry($adminLevel->relation->id);
            foreach ($geometry[''] as $item) {

            }


        }
        //WIP:

        return true;
    }
}
