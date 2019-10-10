<?php

namespace App\Console\Commands;

use App\Geo\OSM;
use App\Models\OsmImports;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OsmPbf\Reader;
use Symfony\Component\Finder\Finder;
use function foo\func;

class OsmImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'osm:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports an OSM file into the database';

    private $inputfolder = 'OsmImport/';
    private $outputfolder = 'OsmImportSQL/';
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
        $path = Storage::path($this->inputfolder);
        $files = File::glob("{$path}*.osm.pbf");
        $this->output->writeln("List of files available to process");
        foreach ($files as $i => $item) {
            $this->output->writeln($i + 1 . ') ' . Str::replaceFirst($path, '', $item));
        }
        $fileindex = $this->ask("Chose the file you want to extract");
        if (!is_numeric($fileindex) || $fileindex < 1 || $fileindex > count($files)) {
            $this->output->writeln("Invalid value it must be a number between 1 and " . count($files));
        }
        $start_time = time();
        $filename = File::basename($files[$fileindex - 1]);
        $filepath = Storage::disk('local')->path($this->inputfolder . $filename);
        if (!Storage::disk('local')->exists($this->inputfolder . $filename)) {
            $this->error('The file ' . $filepath . " does not exist!");
            return false;
        };

        $filehandler = fopen($filepath, "rb");
        $pbfreader = new Reader($filehandler);
        $file_header = $pbfreader->readFileHeader();

        $import = OsmImports::findOrNew($file_header->getOsmosisReplicationBaseUrl());
        $import->bbox_left = $file_header->getBbox()->getLeft() * 0.000000001;
        $import->bbox_bottom = $file_header->getBbox()->getBottom() * 0.000000001;
        $import->bbox_right = $file_header->getBbox()->getRight() * 0.000000001;
        $import->bbox_top = $file_header->getBbox()->getTop() * 0.000000001;
        $import->replication_timestamp = $file_header->getOsmosisReplicationTimestamp();
        $import->replication_sequence = $file_header->getOsmosisReplicationSequenceNumber();
        $import->save();

        $this->outputfolder .= $import->id . " - " . $filename . "/";
        Storage::disk('local')->deleteDirectory($this->outputfolder);
        Storage::disk('local')->makeDirectory($this->outputfolder);

        foreach ($this->handlers as $entity => &$handler) {
            $file_name = Storage::disk('local')->path($this->outputfolder . $entity . ".sql");
            $handler = fopen($file_name, "a + ");
            if (!$handler) {
                return false;
            }
        }
        OsmImports::create();

        $reader = $pbfreader->getReader();
        $total = $reader->getEofPosition();
        $this->output->progressStart($total);
        $last_position = 0;
        while ($pbfreader->next()) {
            $current = $reader->getPosition();
            $this->output->progressAdvance($current - $last_position);
            $elements = $pbfreader->getElements();
            $elements = OSM::processElements($elements);
            $this->writeInserts($elements);
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

    public function writeInserts($elements)
    {
        $type = $elements['type'];
        $chunk_size = 6550;
        foreach (array_chunk($elements['records'], $chunk_size) as $chunk) {
            $this->counts[$type] += count($chunk);
            $sql = OSM::getQuery(Str::plural($type), $chunk);
            fwrite($this->handlers[Str::plural($type)], $sql . "\n");
        }

        foreach (array_chunk($elements['tags'], $chunk_size) as $chunk) {
            $this->counts[$type . "_tags"] += count($chunk);
            $sql = OSM::getQuery($type . "_tags", $chunk);
            fwrite($this->handlers[$type . "_tags"], $sql . "\n");
        }

        foreach (array_chunk($elements['nodes'], $chunk_size) as $chunk) {
            $this->counts["way_nodes"] += count($chunk);
            $sql = OSM::getQuery('way_nodes', $chunk);
            fwrite($this->handlers['way_nodes'], $sql . "\n");
        }

        foreach (array_chunk($elements['relations'], $chunk_size) as $chunk) {
            $this->counts["relation_members"] += count($chunk);
            $sql = OSM::getQuery('relation_members', $chunk);
            fwrite($this->handlers['relation_members'], $sql . "\n");
        }
    }
}
