<?php
/**
 * Created by PhpStorm.
 * User: sierraf
 * Date: 2/19/2019
 * Time: 9:56 AM
 */

namespace App\Models\OSM;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OSM\Way
 *
 * @property int $id
 * @property int $changeset_id
 * @property int $visible
 * @property string $timestamp
 * @property int $version
 * @property string $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OSM\WayNode[] $nodes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OSM\WayTag[] $tags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Way newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Way newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Way query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Way whereChangesetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Way whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Way whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Way whereUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Way whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\Way whereVisible($value)
 * @mixin \Eloquent
 */
class Way extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'changeset_id',
        'visible',
        'timestamp',
        'version',
        'user'
    ];

    public function tags()
    {
        return $this->hasMany(WayTag::class);
    }

    public function nodes()
    {
        return $this->hasMany(WayNode::class);
    }

}