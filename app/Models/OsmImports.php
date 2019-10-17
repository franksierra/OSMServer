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
 * App\Models\OsmImports
 *
 * @property int $id
 * @property float $bbox_left
 * @property float $bbox_bottom
 * @property float $bbox_right
 * @property float $bbox_top
 * @property int $replication_timestamp
 * @property int $replication_sequence
 * @property string $replication_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OsmImports newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OsmImports newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OsmImports query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OsmImports whereBboxBottom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OsmImports whereBboxLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OsmImports whereBboxRight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OsmImports whereBboxTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OsmImports whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OsmImports whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OsmImports whereReplicationSequence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OsmImports whereReplicationTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OsmImports whereReplicationUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OsmImports whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OsmImports extends Model
{

    protected $fillable = [
        'bbox_left',
        'bbox_bottom',
        'bbox_right',
        'bbox_top',
        'replication_timestamp',
        'replication_sequence',
        'replication_url'
    ];

}
