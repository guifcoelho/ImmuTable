<?php

namespace guifcoelho\ImmuTable\Tests\Unit\SampleModels;

use guifcoelho\ImmuTable\Model;

class Sample2 extends Model
{
    protected $table = "test_table2";

    public function owned(){
        return $this->ImmuTable_hasOne(SampleOwned::class);
    }

    public function many_owners(){
        return $this->ImmuTable_belongsToMany(Sample::class);
    }

}

