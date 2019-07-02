<?php

namespace guifcoelho\ImmuTable\Tests\Unit\SampleModels;

use guifcoelho\ImmuTable\Model;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\SampleOwned;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\SampleOwned2;

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

