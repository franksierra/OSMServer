<?php

namespace App\Console\Commands;

use App\Jobs\ProcessElements;
use App\Models\OSM\Node;
use App\Models\OSM\NodeTag;
use App\Models\OSM\OsmSettings;
use App\Models\OSM\Relation;
use App\Models\OSM\RelationMember;
use App\Models\OSM\RelationTag;
use App\Models\OSM\Way;
use App\Models\OSM\WayNode;
use App\Models\OSM\WayTag;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\Storage;

use OsmPbf\Reader;

class OsmImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'osm:import {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports an OSM file into the database';

    private $storagePath = '';
    private $inputfolder = 'OsmImport/';
    private $outputfolder = 'OsmExport/';
    private $outputhandlers = [];

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
        Storage::disk('local')->makeDirectory($this->inputfolder);
        Storage::disk('local')->makeDirectory($this->outputfolder);
        $this->storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();

        $start_time = time();
        $filename = $this->argument('filename');
        $full_file_name = $this->storagePath . $this->inputfolder . $filename;
        if (!Storage::disk('local')->exists($this->inputfolder . $filename)) {
            $this->error('The file ' . $full_file_name . " does not exist!");
            return false;
        };
        $file_handler = fopen($full_file_name, "rb");
        $pbfreader = new Reader($file_handler);

        $file_header = $pbfreader->readFileHeader();
        $bbox_left = 0.000000001 * $file_header->getBbox()->getLeft();
        $bbox_bottom = 0.000000001 * $file_header->getBbox()->getBottom();
        $bbox_right = 0.000000001 * $file_header->getBbox()->getRight();
        $bbox_top = 0.000000001 * $file_header->getBbox()->getTop();

        $replication_timestamp = $file_header->getOsmosisReplicationTimestamp();
        $replication_sequence = $file_header->getOsmosisReplicationSequenceNumber();
        $replication_url = $file_header->getOsmosisReplicationBaseUrl();

//        OsmSettings::create([
//            'country' => "EC",
//            'bbox_left' => $bbox_left,
//            'bbox_bottom' => $bbox_bottom,
//            'bbox_right' => $bbox_right,
//            'bbox_top' => $bbox_top,
//            'replication_timestamp' => $replication_timestamp,
//            'replication_sequence' => $replication_sequence,
//            'replication_url' => $replication_url
//        ]);
        $reader = $pbfreader->getReader();


        $total = $reader->getEofPosition();
        $this->output->progressStart($total);
        $last_position = 0;
        while ($data = $pbfreader->next()) {
            $current = $reader->getPosition();
            $this->output->progressAdvance($current - $last_position);
            $elements = $pbfreader->getElements();
            dispatch(new ProcessElements($elements));
            $last_position = $current;
        }
        $this->output->progressFinish();
        $end_time = time();
        echo "This process took " . ($end_time - $start_time) . " seconds";
        return true;
    }

}
