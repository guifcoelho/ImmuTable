<?php

namespace guifcoelho\ImmuTable\Tests\Unit\SampleModels;

use guifcoelho\ImmuTable\Model;

class Sample2 extends Model
{

    protected $fillable = ['id', 'name', 'email'];

    protected $table = "test_table2";

}

