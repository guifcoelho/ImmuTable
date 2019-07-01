<?php

use Faker\Generator as Faker;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\SampleOwned;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\Sample;

$factory->define(SampleOwned::class, function(Faker $faker){
    return [
        'sample_id' => 1
    ];
});

