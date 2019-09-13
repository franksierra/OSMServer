<?php

namespace App\Console\Commands;

use App\Geo\OSM;
use App\Models\OSM\RelationTag;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OsmFixMissing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'osm:fix-missing';

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
        $nodes =


        $xml = simplexml_load_string($data);
        $json = str_replace('@', '', json_encode($xml));
        $json = json_decode($json);
        unset($json->attributes);
        foreach ($json->node as $item) {

        }



        foreach ($json->node as $item) {
            $data = $this->proccess($item);
//            $this->makeEntity('node', $data);
        }
        if (!is_array($json->way)) {
            $data = $this->proccess($json->way);
//            $this->makeEntity('way', $data);
        } else {
            foreach ($json->way as $item) {
                $data = $this->proccess($item);
//                $this->makeEntity('way', $data);
            }
        }

        return true;
    }

    private function proccess($json)
    {
        $attributes = $json->attributes;

        $dirty_tags = $json->tag ?? [];
        $tags = [];
        foreach ($dirty_tags as $item) {
            if (isset($item->attributes)) {
                $tags[] = [
                    "key" => $item->attributes->k,
                    "value" => $item->attributes->v
                ];
            } else {
                $tags[] = [
                    "key" => $item->k,
                    "value" => $item->v
                ];
            }
        }

        $dirty_nodes = $json->nd ?? [];
        $nodes = [];
        $sequence = 0;
        foreach ($dirty_nodes as $item) {
            $nodes[] = [
                "id" => $item->attributes->ref,
                "sequence" => $sequence
            ];
            $sequence++;
        }

        $dirty_members = $json->member ?? [];
        $members = [];
        $sequence = 0;
        foreach ($dirty_members as $item) {
            $members[] = [
                "member_type" => $item->attributes->type,
                "member_id" => $item->attributes->ref,
                "member_role" => $item->attributes->role,
                "sequence" => $sequence,
            ];
            $sequence++;
        }


        $data = [
            'id' => $attributes->id,
            'latitude' => $attributes->lat ?? "",
            'longitude' => $attributes->lon ?? "",
            'changeset_id' => $attributes->changeset ?? "",
            'visible' => ($attributes->visible ?? "") == "true" ? 1 : 0,
            'timestamp' => $attributes->timestamp ?? "",
            'version' => $attributes->version ?? "",
            'uid' => $attributes->uid ?? "",
            'user' => $attributes->user ?? "",
            "tags" => $tags,
            "nodes" => $nodes,
            "relations" => $members
        ];

        return $data;
    }
}
