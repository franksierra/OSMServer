<?php
/**
 * Created by PhpStorm.
 * User: sierraf
 * Date: 2/19/2019
 * Time: 9:58 AM
 */

namespace App\Models\OSM;


use Illuminate\Database\Eloquent\Model;

class WayTag extends Model
{
    public $timestamps = false;

    public function node()
    {
        return $this->belongsTo(Way::class);
    }

}