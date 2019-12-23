<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Phaza\LaravelPostgis\Eloquent\PostgisTrait;

/**
 * App\Models\TerritorialDivision
 *
 * @property int $relation_id
 * @property int $parent_relation_id
 * @property string $name
 * @property int $admin_level
 * @property string $geometry
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision comparison($geometryColumn, $geometry, $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision contains($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision crosses($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision disjoint($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision distance($geometryColumn, $geometry, $distance)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision distanceExcludingSelf($geometryColumn, $geometry, $distance)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision distanceSphere($geometryColumn, $geometry, $distance)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision distanceSphereExcludingSelf($geometryColumn, $geometry, $distance)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision distanceSphereValue($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision distanceValue($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision doesTouch($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision equals($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision intersects($geometryColumn, $geometry)
 * @method static \Phaza\LaravelPostgis\Eloquent\Builder|\App\Models\TerritorialDivision newModelQuery()
 * @method static \Phaza\LaravelPostgis\Eloquent\Builder|\App\Models\TerritorialDivision newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision orderByDistance($geometryColumn, $geometry, $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision orderByDistanceSphere($geometryColumn, $geometry, $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision orderBySpatial($geometryColumn, $geometry, $orderFunction, $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision overlaps($geometryColumn, $geometry)
 * @method static \Phaza\LaravelPostgis\Eloquent\Builder|\App\Models\TerritorialDivision query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision whereAdminLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision whereGeometry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision whereParentRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision whereRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision within($geometryColumn, $polygon)
 * @mixin \Eloquent
 */
class TerritorialDivision extends Model
{
    use PostgisTrait;

    public $incrementing = false;
    public $timestamps = false;

    protected $primaryKey = "relation_id";

    protected $fillable = [
        'relation_id',
        'parent_relation_id',
        'name',
        'admin_level',
        'geometry'
    ];

    protected $postgisFields = [
        'geometry'
    ];
    protected $postgisTypes = [
        'geometry' => [
            'geomtype' => 'geometry',
            'srid' => 4326
        ],
    ];

}
