<?php

namespace App\Console\Commands;

use App\Models\OSM\Node;
use App\Models\OSM\NodeTag;
use App\Models\OsmImports;
use App\Models\OSM\Relation;
use App\Models\OSM\RelationMember;
use App\Models\OSM\RelationTag;
use App\Models\OSM\Way;
use App\Models\OSM\WayNode;
use App\Models\OSM\WayTag;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;
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
    private $outputfolder = 'OsmExport/';
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
    private $handlers = [
        "nodes" => null,
        "node_tags" => null,
        "ways" => null,
        "way_tags" => null,
        "way_nodes" => null,
        "relations" => null,
        "relation_tags" => null,
        "relation_members" => null,
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
        $this->outputfolder .= $country . "/";
        Storage::disk('local')->deleteDirectory($this->outputfolder);
        Storage::disk('local')->makeDirectory($this->outputfolder);
        foreach ($this->handlers as $entity => &$handler) {
            $file_name = Storage::disk('local')->path($this->outputfolder . $entity . ".sql");
            $handler = fopen($file_name, "a+");
            if (!$handler) {
                return false;
            }
        }

        $file_handler = fopen($full_file_name, "rb");
        $pbfreader = new Reader($file_handler);
        $file_header = $pbfreader->readFileHeader();
        $sql = $this->getQuery('osm_imports', [
            'country' => $country,
            'bbox_left' => $file_header->getBbox()->getLeft() * 0.000000001,
            'bbox_bottom' => $file_header->getBbox()->getBottom() * 0.000000001,
            'bbox_right' => $file_header->getBbox()->getRight() * 0.000000001,
            'bbox_top' => $file_header->getBbox()->getTop() * 0.000000001,
            'replication_timestamp' => $file_header->getOsmosisReplicationTimestamp(),
            'replication_sequence' => $file_header->getOsmosisReplicationSequenceNumber(),
            'replication_url' => $file_header->getOsmosisReplicationBaseUrl()
        ]);
        Storage::append($this->outputfolder . 'osm_imports' . '.sql', $sql . "\n");

        $reader = $pbfreader->getReader();

        $total = $reader->getEofPosition();
        $this->output->progressStart($total);
        $last_position = 0;
        while ($pbfreader->next()) {
            $current = $reader->getPosition();
            $this->output->progressAdvance($current - $last_position);
            $elements = $pbfreader->getElements();
            $this->insertElements($elements);
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
        $chunk_size = 6550;
        foreach (array_chunk($records, $chunk_size) as $chunk) {
            $this->counts[$type] += count($chunk);
            $sql = $this->getQuery(Str::plural($type), $chunk) . "\n";
            fwrite($this->handlers[Str::plural($type)], $sql);
        }

        foreach (array_chunk($tags, $chunk_size) as $chunk) {
            $this->counts[$type . "_tags"] += count($chunk);
            $sql = $this->getQuery($type . "_tags", $chunk) . "\n";
            fwrite($this->handlers[$type . "_tags"], $sql);
        }

        foreach (array_chunk($nodes, $chunk_size) as $chunk) {
            $this->counts["way_nodes"] += count($chunk);
            $sql = $this->getQuery('way_nodes', $chunk) . "\n";
            fwrite($this->handlers['way_nodes'], $sql);
        }

        foreach (array_chunk($relations, $chunk_size) as $chunk) {
            $this->counts["relation_members"] += count($chunk);
            $sql = $this->getQuery('relation_members', $chunk) . "\n";
            fwrite($this->handlers['relation_members'], $sql);
        }
    }


    private function getQuery($table_name, $values)
    {
        $table = DB::table($table_name);
        if (!is_array(reset($values))) {
            $values = [$values];
        }
        $columns = $table->getGrammar()->columnize(array_keys(reset($values)));
        $parameters = collect($values)->map(function ($record) use ($table) {
            $record = array_map('addslashes', $record);
            return '(' . $table->getGrammar()->quoteString($record) . ')';
        })->implode(', ');
        return "insert ignore into $table_name ($columns) values $parameters;";
    }


}
