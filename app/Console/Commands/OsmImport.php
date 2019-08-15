<?php

namespace App\Console\Commands;

use App\Jobs\ProcessElements;
use App\Models\OSM\Node;
use App\Models\OSM\NodeTag;
use App\Models\OSM\OsmImports;
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
    protected $signature = 'osm:import {country} {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports an OSM file into the database';

    private $storagePath = '';
    private $inputfolder = 'OsmImport/';
    private $counts = [
        "node" => 0,
        "node_tags" => 0,
        "way" => 0,
        "way_tags" => 0,
        "way_nodes" => 0,
        "relation" => 0,
        "relation_tags" => 0,
        "relation_members" => 0,
    ];

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
        $this->storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();

        $start_time = time();
        $country = $this->argument('country');
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

        OsmImports::create([
            'country' => $country,
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
        $last_position = 0;
        while ($pbfreader->next()) {
            $current = $reader->getPosition();
            $this->output->progressAdvance($current - $last_position);
            $this->insertElements($pbfreader->getElements());
//        dispatch(new ProcessElements($elements));
            $last_position = $current;
        }
        $this->output->progressFinish();
        $end_time = time();
        $this->output->writeln("This process took " . ($end_time - $start_time) . " seconds");
        $this->output->writeln("Processed Records:");
        $this->output->writeln("Nodes: " . $this->counts["node"]);
        $this->output->writeln("Node Tags: " . $this->counts["node_tags"]);
        $this->output->writeln("Ways: " . $this->counts["way"]);
        $this->output->writeln("Way Tags: " . $this->counts["way_tags"]);
        $this->output->writeln("Way Nodes: " . $this->counts["way_nodes"]);
        $this->output->writeln("Relations: " . $this->counts["relation"]);
        $this->output->writeln("Relation Tags: " . $this->counts["relation_tags"]);
        $this->output->writeln("Relation Members: " . $this->counts["relation_members"]);
        return true;
    }

    public function insertElements($elements)
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
                $tags[] = [
                    $type . "_id" => $element["id"],
                    "k" => $tag["key"],
                    "v" => $tag["value"]
                ];
            }
            foreach ($element["nodes"] as $node) {
                $nodes[] = [
                    $type . "_id" => $element["id"],
                    "node_id" => $node["id"],
                    "sequence" => $node["sequence"]
                ];
            }

            foreach ($element["relations"] as $relation) {
                $relations[] = [
                    $type . "_id" => $element["id"],
                    "member_type" => $relation["member_type"],
                    "member_id" => $relation["member_id"],
                    "member_role" => $relation["member_role"],
                    "sequence" => $relation["sequence"]
                ];
            }
        }
        /** @var Node|Way|Relation $elementObjName */
        $elementObjName = "App\\Models\\OSM\\" . ucfirst($type);
        /** @var NodeTag|WayTag|RelationTag $elementTagName */
        $elementTagName = "App\\Models\\OSM\\" . ucfirst($type) . "Tag";

        $chunk_size = 6550;
        foreach (array_chunk($records, $chunk_size) as $chunk) {
            $this->counts[$type] += count($chunk);
            $elementObjName::insert($chunk);
        }

        foreach (array_chunk($tags, $chunk_size) as $chunk) {
            $this->counts[$type . "_tags"] += count($chunk);
            $elementTagName::insert($chunk);
        }

        foreach (array_chunk($nodes, $chunk_size) as $chunk) {
            $this->counts["way_nodes"] += count($chunk);
            WayNode::insert($chunk);
        }

        foreach (array_chunk($relations, $chunk_size) as $chunk) {
            $this->counts["relation_members"] += count($chunk);
            RelationMember::insert($chunk);
        }
    }
}
