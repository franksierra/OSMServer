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

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        Storage::disk('local')->makeDirectory('importOSM/');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        $filename = $this->argument('filename');
        if (!Storage::disk('local')->exists('importOSM/' . $filename)) {
            $this->error('The file ' . $storagePath . 'importOSM/' . $filename . "does not exist!");
            return false;
        };
        $full_file_name = $storagePath . 'importOSM/' . $filename;

        $file_handler = fopen($full_file_name, "rb");
        $pbfreader = new OSMReader($file_handler);

        $file_header = $pbfreader->readFileHeader();
        $replication_url = $file_header->getOsmosisReplicationBaseUrl();

        /**
         * If you need to, you can skip on some blocks...
         * By specifying a block index
         */
        $index = 852;
        $pbfreader->skipToBlock(0);
        while ($data = $pbfreader->next()) {
            $reader = $pbfreader->getReader();
            $current = $reader->getPosition();
            $total = $reader->getEofPosition();
            echo $index . " - " . $current . "/" . $total . "\t" . round(($current / $total) * 100, 2) . "% ->";
            $index++;
            $elements = $pbfreader->getElements();
            $this->processElements($elements);
            echo "done\n";
        }
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
        $chunk_size = 2000;

        /** @var Node|Way|Relation $elementObjName */
        $elementObjName = "App\\Models\\OSM\\" . ucfirst($type);
        $records = array_chunk($records, $chunk_size);
        foreach ($records as $chunk) {
            $elementObjName::insert($chunk);
        }

        /** @var NodeTag|WayTag|RelationTag $elementTagName */
        $elementTagName = "App\\Models\\OSM\\" . ucfirst($type) . "Tag";
        $tags = array_chunk($tags, $chunk_size);
        foreach ($tags as $chunk) {
            $elementTagName::insert($chunk);
        }

        $nodes = array_chunk($nodes, $chunk_size);
        foreach ($nodes as $chunk) {
            WayNode::insert($chunk);
        }

        $relations = array_chunk($relations, $chunk_size);
        foreach ($relations as $chunk) {
            RelationMember::insert($chunk);
        }

    }

}
