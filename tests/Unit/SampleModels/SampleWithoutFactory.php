<?php

namespace guifcoelho\ImmuTable\Tests\Unit\SampleModels;

use guifcoelho\ImmuTable\Model;

class SampleWithoutFactory extends Model
{

    protected $fields = ['id', 'owner'];

    protected $table = "test_table_without_factory";

}

