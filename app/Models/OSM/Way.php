<?php
/**
 * Created by PhpStorm.
 * User: sierraf
 * Date: 2/19/2019
 * Time: 9:56 AM
 */

namespace App\Models\OSM;


use Illuminate\Database\Eloquent\Model;

class Way extends Model
{
    public $incrementing = false;
    public $timestamps = false;


    public function tags()
    {
        return $this->hasMany(WayTag::class);

    }

    public function nodes()
    {
        return $this->hasMany(WayNode::class);

    }
}