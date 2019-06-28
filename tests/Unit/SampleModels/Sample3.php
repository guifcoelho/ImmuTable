<?php

namespace guifcoelho\JsonModels\Tests\Unit\SampleModels;

use guifcoelho\JsonModels\Model;

class Sample3 extends Model
{

    protected $fillable = ['id', 'name', 'email'];

    protected $fields = ['id', 'name', 'email'];

    protected $table = "test_table3";

}

