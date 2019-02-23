<?php
/**
 * Created by PhpStorm.
 * User: sierraf
 * Date: 2/19/2019
 * Time: 10:23 AM
 */

use Faker\Generator as Faker;
use Faker\Provider\Lorem;

$factory->define(App\Models\OSM\WayTag::class, function (Faker $faker) {
    return [
        "version" => "1",
        "k" => Lorem::word(),
        "v" => Lorem::sentence(6)
    ];
});