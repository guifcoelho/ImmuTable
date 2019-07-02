<?php

use Faker\Generator as Faker;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\Sample3;

$factory->define(Sample3::class, function(Faker $faker){
    return [
        'name' => $faker->name,
    ];
});

