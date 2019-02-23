<?php
/**
 * Created by PhpStorm.
 * User: sierraf
 * Date: 2/19/2019
 * Time: 10:23 AM
 */

use Faker\Generator as Faker;

$factory->define(App\Models\OSM\Way::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->randomNumber(5),
        'visible' => "1",
        'timestamp' => $faker->dateTime('now', "America/Guayaquil"),
        'version' => "1",
    ];
});