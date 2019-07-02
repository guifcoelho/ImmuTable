<?php

namespace guifcoelho\ImmuTable\Tests\Unit\SampleModels;

use guifcoelho\ImmuTable\Model;

class Sample4 extends Model
{

    protected $fillable = ['id', 'name', 'email'];

    protected $fields = ['id', 'name', 'email'];

    protected $table = "test_table4";

}

