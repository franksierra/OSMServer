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
        $start_time = time();
        $filename = $this->argument('filename');
        $full_file_name = $this->storagePath . $this->inputfolder . $filename;
        if (!Storage::disk('local')->exists($this->inputfolder . $filename)) {
            $this->error('The file ' . $full_file_name . "does not exist!");
            return false;
        };
        $this->outputhandlers = [
            "nodes" => null,
            "node_tags" => null,
            "ways" => null,
            "way_tags" => null,
            "way_nodes" => null,
            "relations" => null,
            "relation_tags" => null,
            "relation_members" => null,
        ];
        foreach ($this->outputhandlers as $entity => &$handler) {
            $filename = $this->storagePath . $this->outputfolder . "insert_" . $entity . ".sql";
            if (is_file($filename)) {
                unlink($filename);
            }
            $handler = fopen($filename, "a+");
            if (!$handler) {
                die();
            }
        }
        $file_handler = fopen($full_file_name, "rb");
        $pbfreader = new OSMReader($file_handler);

        $file_header = $pbfreader->readFileHeader();
        $replication_url = $file_header->getOsmosisReplicationBaseUrl();

        /**
         * If you need to, you can skip on some blocks...
         * By specifying a block index
         */
        $index = 0;
        $pbfreader->skipToBlock($index);
        while ($data = $pbfreader->next()) {
            $reader = $pbfreader->getReader();
            $current = $reader->getPosition();
            $total = $reader->getEofPosition();
            echo $index . " - " . $current . "/" . $total . "\t" . round(($current / $total) * 100, 2) . "% -> " . (time() - $start_time);
            $index++;
            $elements = $pbfreader->getElements();
            $this->processElements($elements);
            echo " done\n";
        }
        $end_time = time();
        echo "This process took " . ($end_time - $start_time) . " seconds";
        return true;
    }

    private function processElements($elements, $to = 'file')
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
        if ($to != 'file') {
            $chunk_size = 2000;
            $records = array_chunk($records, $chunk_size);
            $tags = array_chunk($tags, $chunk_size);
            $nodes = array_chunk($nodes, $chunk_size);
            $relations = array_chunk($relations, $chunk_size);
        }

        foreach ($records as $chunk) {
            if ($to != 'file') {
                $elementObjName::insert($chunk);
            } else {
                fwrite($this->outputhandlers[$type . "s"], $this->format_output($type, 's', $chunk));
            }
        }

        foreach ($tags as $chunk) {
            if ($to != 'file') {
                $elementTagName::insert($chunk);
            } else {
                fwrite($this->outputhandlers[$type . "_tags"], $this->format_output($type, '_tags', $chunk));
            }
        }

        foreach ($nodes as $chunk) {
            if ($to != 'file') {
                WayNode::insert($chunk);
            } else {
                fwrite($this->outputhandlers[$type . "_nodes"], $this->format_output($type, '_nodes', $chunk));
            }
        }

        foreach ($relations as $chunk) {
            if ($to != 'file') {
                RelationMember::insert($chunk);
            } else {
                fwrite($this->outputhandlers[$type . "_members"], $this->format_output($type, '_members', $chunk));
            }
        }

    }

    private function format_output($entity, $entity_sufix, $insert_data, $format = 'full_sql')
    {
        $table = $entity . $entity_sufix;
        $escaped_keys = array_map(array($this, 'escape'), array_keys($insert_data));
        $escaped_values = array_map(array($this, 'escape'), array_values($insert_data));

        $return_string = "";
        switch ($format) {
            case 'csv':
                $values = "'" . implode("'; '", $escaped_values) . "'";
                $return_string = $values . "\n";
                break;
            case 'sql':
                $values = "'" . implode("', '", $escaped_values) . "'";
                $return_string = "INSERT INTO " . $table . " VALUES ($values);\n";
                break;
            case "full_sql":
                $keys = implode(", ", $escaped_keys);
                $values = "'" . implode("', '", $escaped_values) . "'";
                $return_string = "INSERT INTO " . $table . " ($keys) VALUES ($values);\n";
                break;
        }
        return $return_string;
    }

    private function escape($string)
    {
        return preg_replace('~[\x00\x0A\x0D\x1A\x22\x27\x5C]~u', '\\\$0', $string);
    }
}
