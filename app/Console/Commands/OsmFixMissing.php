<?php

namespace App\Console\Commands;

use App\Geo\OSM;
use App\Models\OSM\RelationTag;
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
        $xml = simplexml_load_string($data);
        if ($xml == FALSE) {
            return false;
        }
        $full = json_decode(json_encode($xml), TRUE);
//
//        foreach ($json->node as $item) {
//            $data = $this->proccess($item);
//            $this->makeEntity('node', $data);
//        }
//        if (!is_array($json->way)) {
//            $data = $this->proccess($json->way);
//            $this->makeEntity('way', $data);
//        } else {
//            foreach ($json->way as $item) {
//                $data = $this->proccess($item);
//                $this->makeEntity('way', $data);
//            }
//        }
    }
}
