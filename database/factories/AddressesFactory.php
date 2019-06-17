<?php

use Faker\Generator as Faker;

$factory->define(App\Addresses::class, function (Faker $faker) {
    return [
        'idPais' => 840,
        'direccion' => $faker->address,
        'ciudad' => $faker->city,
        'estado' => $fake->state
    ];
});
