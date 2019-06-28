<?php

namespace guifcoelho\JsonModels\Tests\Unit\SampleModels;

use guifcoelho\JsonModels\Model;

class SampleWithoutFactory extends Model
{

    protected $fillable = ['id', 'owner'];

    protected $table = "test_table_without_factory";

}

