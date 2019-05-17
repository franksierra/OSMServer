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
 * App\Models\OSM\RelationTag
 *
 * @property int $relation_id
 * @property string $k
 * @property string $v
 * @property-read \App\Models\OSM\Relation $relation
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\RelationTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\RelationTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\RelationTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\RelationTag whereK($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\RelationTag whereRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\RelationTag whereV($value)
 * @mixin \Eloquent
 */
class RelationTag extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'relation_id',
        'k',
        'v'
    ];

    public function relation()
    {
        return $this->belongsTo(Relation::class);
    }

}