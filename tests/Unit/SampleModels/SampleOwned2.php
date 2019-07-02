<?php

namespace guifcoelho\ImmuTable\Tests\Unit\SampleModels;

use guifcoelho\ImmuTable\Model;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\Sample;

class SampleOwned2 extends Model
{

    protected $fillable = ['id', 'owner'];

    protected $table = "test_table_owned2";

    public function owner(){
        return $this->belongsTo(Sample::class, 'owner');
    }

}

