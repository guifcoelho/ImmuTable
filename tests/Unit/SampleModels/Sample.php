<?php

namespace guifcoelho\JsonModels\Tests\Unit\SampleModels;

use guifcoelho\JsonModels\Model;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\SampleOwned;

class Sample extends Model
{

    protected $fillable = ['id', 'name', 'email'];

    protected $table = "test_table";

    public function owned(){
        return $this->hasManyJsonModel(SampleOwned::class);
    }

}

