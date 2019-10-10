<?php


namespace App\Http\Controllers\Api\v1;


use App\Http\Controllers\Controller;
use App\Models\OSM\Node;
use App\Models\OSM\WayTag;
use App\Models\TerritorialDivision;
use Illuminate\Http\Request;

class Street extends Controller
{

    public function index(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'relation_id' => 'nullable|email|max:255'
        ]);

        Node::create();

        $way_search = WayTag::where('k', '=', 'highway')->get();
        $w = $way_search->way();
        dd($w);
//            WayTag::where('k', '=', 'name')
//                ->whereRaw("MATCH ( v ) AGAINST ( ? )", [$request->get('name')])
//                ->where('v', 'like', "%{$request->get('name')}%")
//                ->join('');

//        if (isset($request->filter)) {
//            $filter = TerritorialDivision::find($request->get('relation_id'));
//            if ($filter) {
//                $way_search = $way_search->whereRaw(
//                    "ST_CONTAINS (?, Point ( nodes.longitude, nodes.latitude ) )",
//                    [$filter->geometry]
//                );
//            }
//        }

        /*
SELECT
	way_tags.way_id,
	way_tags.v
FROM
	way_tags
-- INNER JOIN way_nodes ON way_tags.way_id = way_nodes.way_id
-- INNER JOIN nodes ON nodes.id = way_nodes.node_id
WHERE
	way_tags.k = 'name'
	-- AND MATCH ( way_tags.v ) AGAINST ( "9" )
	AND way_tags.v LIKE '9 de oct%'
-- AND ST_CONTAINS ( ( SELECT geometry FROM territorial_divisions WHERE relation_id = 2403848 ), Point ( nodes.longitude, nodes.latitude ) )
         */




        $result = $way_search->get();
        dd($result);

        $response = [
            'success' => true,
            'data' => "",
            'message' => "",
        ];
        return response()->json($response, 200);
    }
}
