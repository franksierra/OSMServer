<?php

namespace App\Console\Commands;

use App\Geo\OSM;
use App\Models\OSM\Node;
use App\Models\OSM\RelationTag;
use App\Models\OSM\Way;
use App\Models\OSM\WayNode;
use App\Models\OSM\WayTag;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

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

    private $cache_dir = 'OsmFixCache/';

    private $guzzle_client = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->guzzle_client = new Client();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Storage::disk('local')->makeDirectory($this->cache_dir);
        $territories = RelationTag::where('k', '=', 'admin_level')
            ->whereIn('v', [2])
            ->orderBy('relation_id', 'ASC')->get();
        foreach ($territories as $territory) {
            $geometry = OSM::relationGeometry($territory->relation->id);
            foreach ($geometry->empty_ways as $id => $nil) {
                $data = $this->getWay($id);
                $this->fixWay($data);
            }
        }

        return true;
    }

    private function getWay($id)
    {
        $file = $this->cache_dir . 'way-' . $id . '.xml';
        if (!Storage::exists($file)) {
            $api_url = "https://www.openstreetmap.org/api/0.6/way/" . $id . "/full";
            $result = $this->guzzle_client->get($api_url, ['verify' => false]);
            $xml = (string)$result->getBody();
            Storage::append($file, $xml);
        } else {
            try {
                $xml = Storage::get($file);
            } catch (FileNotFoundException $e) {
                return false;
            }
        }

        return $xml;
    }

    private function fixWay($data)
    {
        $xml = simplexml_load_string($data);
        $json = str_replace('@', '', json_encode($xml));
        $json = json_decode($json);
        unset($json->attributes);
        foreach ($json->node ?? [] as $item) {
            $element = [
                'id' => $item->attributes->id,
                'latitude' => $item->attributes->lat,
                'longitude' => $item->attributes->lon,
                'changeset_id' => $item->attributes->changeset,
                'visible' => ($item->attributes->visible ?? "") == "true" ? 1 : 0,
                'timestamp' => $item->attributes->timestamp,
                'version' => $item->attributes->version,
                'uid' => $item->attributes->uid,
                'user' => $item->attributes->user,
            ];
            $element["timestamp"] = str_replace("T", " ", $element["timestamp"]);
            $element["timestamp"] = str_replace("Z", "", $element["timestamp"]);
            Node::insertOrIgnore($element);
        }
        if (!is_array($json->way)) {
            $element = [
                'id' => $json->way->attributes->id,
                'changeset_id' => $json->way->attributes->changeset,
                'visible' => ($json->way->attributes->visible ?? "") == "true" ? 1 : 0,
                'timestamp' => $json->way->attributes->timestamp,
                'version' => $json->way->attributes->version,
                'uid' => $json->way->attributes->uid,
                'user' => $json->way->attributes->user,
            ];
            $element["timestamp"] = str_replace("T", " ", $element["timestamp"]);
            $element["timestamp"] = str_replace("Z", "", $element["timestamp"]);
            Way::insertOrIgnore($element);

            foreach ($json->way->tag ?? [] as $tag) {
                if (isset($tag->attributes)) {
                    $way_tag = [
                        'way_id' => $element['id'],
                        'k' => $tag->attributes->k,
                        'v' => $tag->attributes->v
                    ];
                    WayTag::insert($way_tag);
                } else {
                    $way_tag = [
                        'way_id' => $element['id'],
                        "k" => $tag->k,
                        "v" => $tag->v
                    ];
                    WayTag::insert($way_tag);
                }
            }
            $sequence = 0;
            foreach ($json->way->nd ?? [] as $node) {
                $way_node = [
                    'way_id' => $element['id'],
                    'node_id' => $node->attributes->ref,
                    'sequence' => $sequence
                ];
                WayNode::insertOrIgnore($way_node);
                $sequence++;
            }
        } else {
            $o = 0;
        }
        return true;
    }
}
