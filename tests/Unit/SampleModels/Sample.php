<?php

namespace guifcoelho\JsonModels\Tests\Unit\SampleModels;

use guifcoelho\JsonModels\Model;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\SampleOwned;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\SampleOwned2;

class Sample extends Model
{

    protected $fillable = ['id', 'name', 'email'];

    protected $table = "test_table";

    public function owned(){
        return $this->hasMany(SampleOwned::class);
    }

    public function owned2(){
        return $this->hasMany(SampleOwned2::class, 'owner');
    }

}

