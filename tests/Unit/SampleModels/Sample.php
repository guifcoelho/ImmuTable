<?php

namespace guifcoelho\ImmuTable\Tests\Unit\SampleModels;

use guifcoelho\ImmuTable\Relations\ImmuTableRelations;

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

    public function many_owners_diffname(){
        return $this->ImmuTable_belongsToMany(Sample2::class, 'samples_sample2s');
    }

    public function many_owners_diffname_difffield(){
        return $this->ImmuTable_belongsToMany(Sample2::class, 'many_owners', 'child');
    }

    public function many_owners_diffname_difffield_diffparentfield(){
        return $this->ImmuTable_belongsToMany(Sample2::class, 'many_owners2', 'child', 'parent');
    }

    public function many_owners_diffname_diff_model_field(){
        return $this->ImmuTable_belongsToMany(Sample2::class, 'many_owners3', 'email', 'sample2_id', 'email');
    }

    public function many_owners_diffname_diff_model_field_and_diff_parent_model_field(){
        return $this->ImmuTable_belongsToMany(Sample2::class, 'many_owners4', 'sample_email', 'sample2_email', 'email', 'email');
    }

}

