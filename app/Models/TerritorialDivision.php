<?php


namespace App\Models;


use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

class TerritorialDivision extends Model
{
    use SpatialTrait;

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'relation_id',
        'parent_relation_id',
        'name',
        'geometry'
    ];

    protected $spatialFields = [
        'geometry'
    ];

}
