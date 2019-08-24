<?php
/**
 * Created by PhpStorm.
 * User: sierraf
 * Date: 2/19/2019
 * Time: 9:56 AM
 */

namespace App\Models;


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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OsmImports newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OsmImports newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OsmImports query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OsmImports whereBboxBottom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OsmImports whereBboxLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OsmImports whereBboxRight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\OsmImports whereBboxTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\OsmImports whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\OsmImports whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\OsmImports whereReplicationSequence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\OsmImports whereReplicationTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\OsmImports whereReplicationUrl($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\OsmImports whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OSM\OsmImports whereUpdatedAt($value)
 */
class OsmImports extends Model
{
    public $incrementing = false;

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
