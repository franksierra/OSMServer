<?php
/**
 * Created by PhpStorm.
 * User: sierraf
 * Date: 2/19/2019
 * Time: 9:56 AM
 */

namespace App\Models\OSM;


use Illuminate\Database\Eloquent\Model;
use test\Mockery\ReturnTypeObjectTypeHint;

/**
 * App\Models\OSM\Node
 *
 * @property int $id
 * @property float $latitude
 * @property float $longitude
 * @property int $changeset_id
 * @property int $visible
 * @property string $timestamp
 * @property int $version
 * @property int $uid
 * @property string $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OSM\NodeTag[] $tags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Node newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Node newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Node query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Node whereChangesetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Node whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Node whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Node whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Node whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Node whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Node whereUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Node whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Node whereVisible($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OSM\Relation[] $relations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OSM\Way[] $ways
 */
class Node extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'latitude',
        'longitude',
        'changeset_id',
        'visible',
        'timestamp',
        'version',
        'user'
    ];

}
