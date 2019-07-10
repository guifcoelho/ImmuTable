<?php

namespace guifcoelho\ImmuTable\Tests\Unit\SampleModels;

use guifcoelho\ImmuTable\ImmuTableRelations;

use guifcoelho\ImmuTable\Model;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\SampleOwned;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\SampleOwned2;

class Sample extends Model
{
    use ImmuTableRelations;

    protected $fillable = ['id', 'name', 'email'];

    protected $table = "test_table";

    public function owned(){
        return $this->ImmuTable_hasMany(SampleOwned::class);
    }

    public function owned2(){
        return $this->ImmuTable_hasMany(SampleOwned2::class, 'owner');
    }

    public function many_owners(){
        return $this->ImmuTable_belongsToMany(Sample2::class);
    }

}

