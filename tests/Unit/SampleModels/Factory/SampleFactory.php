<?php

use Faker\Generator as Faker;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\Sample;

$factory->define(Sample::class, function(Faker $faker){
    return [
        'name' => $faker->name,
        'email' => $faker->email
    ];
});

