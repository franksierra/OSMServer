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
 * App\Models\OSM\NodeTag
 *
 * @property int $node_id
 * @property string $k
 * @property string $v
 * @property-read \App\Models\OSM\Node $nodes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\NodeTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\NodeTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\NodeTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\NodeTag whereK($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\NodeTag whereNodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\NodeTag whereV($value)
 * @mixin \Eloquent
 */
class NodeTag extends Model
{
    public $timestamps = false;
    protected $primaryKey = "node_id";
    protected $fillable = [
        'node_id',
        'k',
        'v'
    ];

    public function nodes()
    {
        return $this->belongsTo(Node::class);
    }

}
