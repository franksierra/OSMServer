<?php

namespace App\Console\Commands;

use App\Models\OSM\Node;
use App\Models\OSM\Relation;
use App\Models\OSM\Way;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\Storage;
use OSMPBF\OSMReader;

class ImportOSM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:osm {filename}';

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
        $index = 0;
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

            /** @var Node|Way|Relation $elementObjName */
            $elementObjName = "App\\Models\\OSM\\" . ucfirst($type);
            $elementObj = $elementObjName::create($insert_element);
            foreach ($element["tags"] as $tag) {
                $insert_tag = [
                    $type . "_id" => $element["id"],
                    "k" => $tag["key"],
                    "v" => $tag["value"]
                ];
                $elementObj->tags()->create($insert_tag);
            }

            foreach ($element["nodes"] as $node) {
                $insert_node = [
                    $type . "_id" => $element["id"],
                    "node_id" => $node["id"],
                    "sequence" => $node["sequence"]
                ];
                $elementObj->nodes()->create($insert_node);
            }
            foreach ($element["relations"] as $relation) {
                $insert_relation = [
                    $type . "_id" => $element["id"],
                    "member_type" => $relation["member_type"],
                    "member_id" => $relation["member_id"],
                    "member_role" => $relation["member_role"],
                    "sequence" => $relation["sequence"]
                ];
                $elementObj->members()->create($insert_relation);
            }

        }
    }

}
