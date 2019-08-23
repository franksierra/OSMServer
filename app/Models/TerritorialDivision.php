<?php


namespace App\Models;


use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TerritorialDivision
 *
 * @property int $relation_id
 * @property int $parent_relation_id
 * @property string $name
 * @property string|null $geom
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
 * @method static \Grimzy\LaravelMysqlSpatial\Eloquent\Builder|\App\Models\TerritorialDivision newModelQuery()
 * @method static \Grimzy\LaravelMysqlSpatial\Eloquent\Builder|\App\Models\TerritorialDivision newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision orderByDistance($geometryColumn, $geometry, $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision orderByDistanceSphere($geometryColumn, $geometry, $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision orderBySpatial($geometryColumn, $geometry, $orderFunction, $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision overlaps($geometryColumn, $geometry)
 * @method static \Grimzy\LaravelMysqlSpatial\Eloquent\Builder|\App\Models\TerritorialDivision query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision whereGeom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision whereParentRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision whereRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TerritorialDivision within($geometryColumn, $polygon)
 * @mixin \Eloquent
 */
class TerritorialDivision extends Model
{
    use SpatialTrait;

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'relation_id',
        'parent_relation_id',
        'name',
        'geometry'
    ];

    protected $spatialFields = [
        'geometry'
    ];

}
