<?php
/**
 * Created by PhpStorm.
 * User: sierraf
 * Date: 2/19/2019
 * Time: 9:58 AM
 */

namespace App\Models\OSM;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OSM\WayTag
 *
 * @property int $way_id
 * @property string $k
 * @property string $v
 * @property-read \App\Models\OSM\Way $tag
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\WayTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\WayTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\WayTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\WayTag whereK($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\WayTag whereV($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\WayTag whereWayId($value)
 * @mixin \Eloquent
 */
class WayTag extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'way_id',
        'k',
        'v'
    ];

    public function tag()
    {
        return $this->belongsTo(Way::class);
    }

}