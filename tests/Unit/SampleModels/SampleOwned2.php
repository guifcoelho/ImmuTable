<?php

namespace guifcoelho\JsonModels\Tests\Unit\SampleModels;

use guifcoelho\JsonModels\Model;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\Sample;

class SampleOwned2 extends Model
{

    protected $fillable = ['id', 'owner'];

    protected $table = "test_table_owned2";

    public function owner(){
        return $this->belongsToOne(Sample::class, 'owner');
    }

}

