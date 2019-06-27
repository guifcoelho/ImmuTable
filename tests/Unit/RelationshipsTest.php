<?php

namespace guifcoelho\JsonModels\Tests\Unit;

use guifcoelho\JsonModels\Tests\TestCase;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\Sample;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\SampleOwned;
use guifcoelho\JsonModels\Testing\TestingJsonModels;

class RelationshipsTest extends TestCase
{
    use TestingJsonModels;

    public function test_belongsToOne_relationship(){
        $owner = jsonModelFactory(Sample::class,$this->factory_path)->create();
        $owned = jsonModelFactory(SampleOwned::class, $this->factory_path)->create([
            'sample_id' => $owner->id
        ]);
        $this->assertTrue($owned->owner()->id == $owner->id);
    }

    public function test_hasMany_relationship(){
        $dummy = jsonModelFactory(Sample::class, 5, $this->factory_path)->create();
        $owner = $dummy->extract()[0];
        $owned = jsonModelFactory(SampleOwned::class, 10, $this->factory_path)->create([
            'sample_id' => $owner->id
        ]);
        $this->assertTrue(Sample::all()->count() > 0);
        $collection_owned = $owner->owned();
        $this->assertTrue($collection_owned->count() == 10);
        foreach($collection_owned->extract() as $item){
            $this->assertTrue($item->sample_id == $owner->id);
        }
    }
}
