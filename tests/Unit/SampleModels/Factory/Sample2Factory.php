<?php

use Faker\Generator as Faker;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\Sample2;

$factory->define(Sample2::class, function(Faker $faker){
    return [
        'name' => $faker->name,
        'email' => $faker->email
    ];
});

