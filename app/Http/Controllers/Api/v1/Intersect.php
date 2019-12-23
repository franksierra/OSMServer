<?php


namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\OSM\WayNode;
use App\Models\OSM\WayTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Intersect extends Controller
{
    public function index(Request $request)
    {
        $this->validate($request, [
            'q' => 'nullable|min:0|max:255',
            'ways' => 'required'
        ]);
        $ways_array = explode(',', $request->get('ways'));

        $name_query = WayTag::select('v')
            ->where('k', '=', 'name')
            ->whereRaw('way_id IN (way_nodes.way_id)');

        $highway_query = WayTag::select('k')
            ->where('k', '=', 'highway')
            ->whereRaw('way_id IN (way_nodes.way_id)');

        $ways_query = WayNode::select(['node_id', 'way_id'])
            ->selectSub($name_query, 'name')
            ->selectSub($highway_query, 'highway')
            ->whereIn('way_nodes.node_id',
                WayNode::select('node_id')->whereIn('way_nodes.way_id', $ways_array)
            )
            ->whereNotIn('way_nodes.way_id', $ways_array);

        $ways = DB::query()
            ->select([DB::raw('array_agg(node_id) as nodes'), 'name'])
            ->from($ways_query, 'foo')
            ->whereNotNull('name')
            ->whereNotNull('highway')
            ->whereRaw('lower(unaccent(name)) like lower(unaccent(?))',[$request->get('q') . "%"])
            ->groupBy(['name'])
            ->get();

        $ways = $ways->map(function ($record) {
            $record->nodes = Str::replaceFirst("{", "", $record->nodes);
            $record->nodes = Str::replaceLast("}", "", $record->nodes);
            return $record;
        });
        $response = [
            'success' => true,
            'data' => $ways
        ];
        return response()->json($response, 200);
    }
}
