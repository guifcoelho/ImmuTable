<?php

namespace guifcoelho\ImmuTable\Tests\Unit\SampleModels;

use guifcoelho\ImmuTable\Model;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\Sample;

class SampleOwned extends Model
{

    protected $fillable = ['id', 'sample_id'];

    protected $table = "test_table_owned";

    public function owner(){
        return $this->belongsToOne(Sample::class);
    }

}

