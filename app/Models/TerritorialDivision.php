<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class TerritorialDivision extends Model
{

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'relation_id',
        'parent_relation_id',
        'name',
        'geometry'
    ];

}
