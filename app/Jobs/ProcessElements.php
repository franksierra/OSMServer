<?php

namespace App\Jobs;

use App\Models\OSM\Node;
use App\Models\OSM\NodeTag;
use App\Models\OSM\Relation;
use App\Models\OSM\RelationMember;
use App\Models\OSM\RelationTag;
use App\Models\OSM\Way;
use App\Models\OSM\WayNode;
use App\Models\OSM\WayTag;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessElements implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $elements;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($elements)
    {
        $this->elements = $elements;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $type = $this->elements['type'];

        $records = [];
        $tags = [];
        $nodes = [];
        $relations = [];

        foreach ($this->elements['data'] as $element) {
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
            $elementObjName::insert($chunk);
        }

        foreach (array_chunk($tags, $chunk_size) as $chunk) {
            $elementTagName::insert($chunk);
        }

        foreach (array_chunk($nodes, $chunk_size) as $chunk) {
            WayNode::insert($chunk);
        }

        foreach (array_chunk($relations, $chunk_size) as $chunk) {
            RelationMember::insert($chunk);
        }
    }
}
