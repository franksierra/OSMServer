<?php

namespace App\Providers;

use App\Models\OSM\Node as OSMNode;
use App\Models\OSM\Way as OSMWay;
use App\Models\OSM\Relation as OSMRelation;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'node' => OSMNode::class,
            'way' => OSMWay::class,
            'relation' => OSMRelation::class,
        ]);
    }
}
