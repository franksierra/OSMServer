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
 * App\Models\OSM\OsmSettings
 *
 * @property int $id
 * @property string $country
 * @property float $bbox_left
 * @property float $bbox_bottom
 * @property float $bbox_right
 * @property float $bbox_top
 * @property string $replication_timestamp
 * @property int $replication_sequence
 * @property string $replication_url
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\OsmSettings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\OsmSettings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\OsmSettings query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\OsmSettings whereBboxBottom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\OsmSettings whereBboxLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\OsmSettings whereBboxRight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\OsmSettings whereBboxTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\OsmSettings whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\OsmSettings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\OsmSettings whereReplicationSequence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\OsmSettings whereReplicationTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\OsmSettings whereReplicationUrl($value)
 * @mixin \Eloquent
 */
class OsmSettings extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'country',
        'bbox_left',
        'bbox_bottom',
        'bbox_right',
        'bbox_top',
        'replication_timestamp',
        'replication_sequence',
        'replication_url'
    ];

}