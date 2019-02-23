<?php
/**
 * Created by PhpStorm.
 * User: sierraf
 * Date: 2/19/2019
 * Time: 9:56 AM
 */

namespace App\Models\OSM;


use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    public $incrementing = false;
    public $timestamps = false;


    public function tags()
    {
        return $this->hasMany(NodeTag::class);
    }

    public function ways()
    {
        return $this->hasManyThrough(WayNode::class, Way::class);
    }

    public function way_nodes()
    {
        return $this->hasMany(WayNode::class);
    }


}