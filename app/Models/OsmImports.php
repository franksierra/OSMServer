<?php
/**
 * Created by PhpStorm.
 * User: sierraf
 * Date: 2/19/2019
 * Time: 9:56 AM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
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
 * @method static Builder|OsmImports newModelQuery()
 * @method static Builder|OsmImports newQuery()
 * @method static Builder|OsmImports query()
 * @method static Builder|OsmImports whereBboxBottom($value)
 * @method static Builder|OsmImports whereBboxLeft($value)
 * @method static Builder|OsmImports whereBboxRight($value)
 * @method static Builder|OSM\OsmImports whereBboxTop($value)
 * @method static Builder|OSM\OsmImports whereCountry($value)
 * @method static Builder|OSM\OsmImports whereId($value)
 * @method static Builder|OSM\OsmImports whereReplicationSequence($value)
 * @method static Builder|OSM\OsmImports whereReplicationTimestamp($value)
 * @method static Builder|OSM\OsmImports whereReplicationUrl($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|OSM\OsmImports whereCreatedAt($value)
 * @method static Builder|OSM\OsmImports whereUpdatedAt($value)
 */
class OsmImports extends Model
{

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
