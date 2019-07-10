<?php

use Faker\Generator as Faker;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\SampleOwned;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\Sample;

$factory->define(SampleOwned::class, function(Faker $faker){
    return [
        'sample_id' => -1,
        'sample2_id' => -1
    ];
});

