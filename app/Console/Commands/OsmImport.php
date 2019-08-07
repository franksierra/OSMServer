<?php

namespace App\Console\Commands;

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

        OsmSettings::create([
            'country' => "EC",
            'bbox_left' => $bbox_left,
            'bbox_bottom' => $bbox_bottom,
            'bbox_right' => $bbox_right,
            'bbox_top' => $bbox_top,
            'replication_timestamp' => $replication_timestamp,
            'replication_sequence' => $replication_sequence,
            'replication_url' => $replication_url
        ]);
        $reader = $pbfreader->getReader();

        $total = $reader->getEofPosition();
        $this->output->progressStart($total);
        while ($data = $pbfreader->next()) {
            $current = $reader->getPosition();
            $this->output->progressAdvance($current);
            $elements = $pbfreader->getElements();
//            $this->processElements($elements);
        }
        $this->output->progressFinish();
        $end_time = time();
        echo "This process took " . ($end_time - $start_time) . " seconds";
        return true;
    }

    private function processElements($elements)
    {
        $type = $elements['type'];

        $records = [];
        $tags = [];
        $nodes = [];
        $relations = [];

        foreach ($elements['data'] as $element) {
            $insert_element = [
                'id' => $element['id'],
                'changeset_id' => $element['changeset_id'],
                'visible' => $element['visible'],
                'timestamp' => $element['timestamp'],
                'version' => $element['version'],
                'uid' => $element['uid'],
                'user' => $element['user'],
            ];
            if ($type == "node") {
                $insert_element["latitude"] = $element["latitude"];
                $insert_element["longitude"] = $element["longitude"];
            }
            if (isset($element["timestamp"])) {
                $insert_element["timestamp"] = str_replace("T", " ", $element["timestamp"]);
                $insert_element["timestamp"] = str_replace("Z", "", $element["timestamp"]);
            }
            $records[] = $insert_element;

            foreach ($element["tags"] as $tag) {
                $insert_tag = [
                    $type . "_id" => $element["id"],
                    "k" => $tag["key"],
                    "v" => $tag["value"]
                ];
                $tags[] = $insert_tag;
            }
            foreach ($element["nodes"] as $node) {
                $insert_node = [
                    $type . "_id" => $element["id"],
                    "node_id" => $node["id"],
                    "sequence" => $node["sequence"]
                ];
                $nodes[] = $insert_node;
            }

            foreach ($element["relations"] as $relation) {
                $insert_relation = [
                    $type . "_id" => $element["id"],
                    "member_type" => $relation["member_type"],
                    "member_id" => $relation["member_id"],
                    "member_role" => $relation["member_role"],
                    "sequence" => $relation["sequence"]
                ];
                $relations[] = $insert_relation;
            }
        }
        /** @var Node|Way|Relation $elementObjName */
        $elementObjName = "App\\Models\\OSM\\" . ucfirst($type);
        /** @var NodeTag|WayTag|RelationTag $elementTagName */
        $elementTagName = "App\\Models\\OSM\\" . ucfirst($type) . "Tag";

        $chunk_size = 3000;
        $records = array_chunk($records, $chunk_size);
        $tags = array_chunk($tags, $chunk_size);
        $nodes = array_chunk($nodes, $chunk_size);
        $relations = array_chunk($relations, $chunk_size);

        foreach ($records as $chunk) {
            $elementObjName::insert($chunk);
        }

        foreach ($tags as $chunk) {
            $elementTagName::insert($chunk);
        }

        foreach ($nodes as $chunk) {
            WayNode::insert($chunk);
        }

        foreach ($relations as $chunk) {
            RelationMember::insert($chunk);
        }

    }
}
