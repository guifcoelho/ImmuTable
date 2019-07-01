<?php

use Faker\Generator as Faker;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\SampleOwned2;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\Sample;

$factory->define(SampleOwned2::class, function(Faker $faker){
    return [
        'owner' => 1
    ];
});

