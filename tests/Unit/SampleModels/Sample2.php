<?php

namespace guifcoelho\JsonModels\Tests\Unit\SampleModels;

use guifcoelho\JsonModels\Model;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\SampleOwned;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\SampleOwned2;

class Sample2 extends Model
{

    protected $fillable = ['id', 'name', 'email'];

    protected $table = "test_table2";

}

