<?php

namespace guifcoelho\JsonModels\Tests\Unit\SampleModels;

use guifcoelho\JsonModels\Model;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\Sample;

class SampleOwned extends Model
{

    protected $fillable = ['id', 'sample_id'];

    protected $table = "test_table_owned";

    public function owner(){
        return $this->belongsToOneJsonModel(Sample::class);
    }

}

