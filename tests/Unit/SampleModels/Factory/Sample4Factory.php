<?php

use Faker\Generator as Faker;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\Sample4;

$factory->define(Sample4::class, function(Faker $faker){
    return [
        'name' => $faker->name,
        'email' => $faker->email,
    ];
});

