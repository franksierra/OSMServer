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
 * App\Models\OSM\NodeWay
 *
 * @property int $node_id
 * @property int $way_id
 * @property int $sequence
 * @property-read \App\Models\OSM\Node $node
 * @property-read \App\Models\OSM\Way $way
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\WayNode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\WayNode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\WayNode query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\WayNode whereNodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\WayNode whereSequence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\WayNode whereWayId($value)
 * @mixin \Eloquent
 */
class WayNode extends Model
{

    public $timestamps = false;
    protected $primaryKey = "way_id";
    protected $fillable = [
        'way_id',
        'node_id',
        'sequence'
    ];

    public function nodes()
    {
        return $this->hasMany(Node::class, 'id');
    }

}
