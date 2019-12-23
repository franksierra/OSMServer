<?php


namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\OSM\Node;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Geocode extends Controller
{

    public function index(Request $request)
    {
        $this->validate($request, [
            'nodes' => 'required'
        ]);
        $nodes = explode(',', $request->get('nodes'));
        $coord = Node::select(
            [
                DB::raw('AVG(latitude) as latitude'),
                DB::raw('AVG(longitude) as longitude'),
            ])
            ->whereIn('id', $nodes)
            ->get();
        $response = [
            'success' => true,
            'data' => $coord
        ];
        return response()->json($response, 200);
    }

    public function reverse()
    {

    }

}
