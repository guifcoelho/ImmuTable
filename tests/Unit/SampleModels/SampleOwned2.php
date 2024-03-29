<?php

namespace guifcoelho\ImmuTable\Tests\Unit\SampleModels;

use guifcoelho\ImmuTable\Model;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\Sample;

class SampleOwned2 extends Model
{
    protected $fields = ['id', 'owner'];

    protected $table = "test_table_owned2";

    public function owner(){
        return $this->ImmuTable_belongsTo(Sample::class, 'owner');
    }

}

