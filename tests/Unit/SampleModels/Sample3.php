<?php

namespace guifcoelho\ImmuTable\Tests\Unit\SampleModels;

use guifcoelho\ImmuTable\Model;

class Sample3 extends Model
{
    protected $fields = ['id', 'name', 'email'];

    protected $table = "test_table3";

}

