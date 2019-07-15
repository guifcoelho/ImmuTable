<?php

namespace guifcoelho\ImmuTable\Tests\Unit\SampleModels;

use guifcoelho\ImmuTable\Relations\ImmuTableRelations;

use guifcoelho\ImmuTable\Model;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\Sample;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\Sample2;

class SampleOwned extends Model
{
    use ImmuTableRelations;

    protected $fields = ['id', 'sample_id', 'sample2_id'];

    protected $table = "test_table_owned";

    public function owner(){
        return $this->ImmuTable_belongsTo(Sample::class);
    }

    public function owner2(){
        return $this->ImmuTable_belongsTo(Sample2::class);
    }

}

