<?php

namespace App\Console\Commands;

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

class OsmFixMissing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'osm:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports an OSM file into the database';

    private $storagePath = '';
    private $inputfolder = 'OsmFixCache/';
    private $outputfolder = 'OsmFixExport/';
    private $outputhandlers = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        Storage::disk('local')->makeDirectory($this->inputfolder);
        Storage::disk('local')->makeDirectory($this->outputfolder);
        $this->storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $filename = $this->argument('filename');
        $full_file_name = $this->storagePath . $this->inputfolder . $filename;
        if (!Storage::disk('local')->exists($this->inputfolder . $filename)) {
            $this->error('The file ' . $full_file_name . "does not exist!");
            return false;
        };

        //WIP:

        return true;
    }
}
