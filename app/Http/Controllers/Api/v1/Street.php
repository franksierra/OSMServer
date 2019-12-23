<?php


namespace App\Http\Controllers\Api\v1;


use App\Geo\OSM;
use App\Http\Controllers\Controller;
use App\Models\OSM\Node;
use App\Models\OSM\Way;
use App\Models\OSM\WayTag;
use App\Models\TerritorialDivision;
use Foolz\SphinxQL\SphinxQL;
use Illuminate\Http\Request;

class Street extends Controller
{

    public function index(Request $request)
    {
        $this->validate($request, [
            'q' => 'required|min:1|max:255',
            'relation_id' => 'required|exists:territorial_divisions,relation_id'
        ]);
        $shape = $geom = TerritorialDivision::find($request->get('relation_id'));
        $shape = OSM::convert((string)$shape->geometry);
        $shape = implode(",", $shape);

        $ways = Way::search("{$request->get('q')}", function (SphinxQL $query) use ($shape) {
            $query->setSelect("
                group_concat(id) as ways, 
                name, 
                CONTAINS(GEOPOLY2D({$shape}), longitude , latitude) as inside,
                WEIGHT() as weight
            ");
            $query->groupBy("name");
            $query->where("inside", "=", 1);
        })->raw()->fetchAllAssoc();
        $response = [
            'success' => true,
            'data' => $ways
        ];
        return response()->json($response, 200);
    }
}
