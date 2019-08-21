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
 * App\Models\OSM\RelationMember
 *
 * @property int $relation_id
 * @property string $member_type
 * @property int $member_id
 * @property string $member_role
 * @property int $sequence
 * @property-read \App\Models\OSM\Relation $relation
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\RelationMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\RelationMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\RelationMember query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\RelationMember whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\RelationMember whereMemberRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\RelationMember whereMemberType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\RelationMember whereRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\RelationMember whereSequence($value)
 * @mixin \Eloquent
 */
class RelationMember extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'relation_id',
        'member_type',
        'member_id',
        'member_role',
        'sequence'
    ];

}
