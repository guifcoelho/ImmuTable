<?php

namespace guifcoelho\JsonModels\Tests\Unit;

use guifcoelho\JsonModels\Tests\TestCase;

use guifcoelho\JsonModels\Tests\Unit\SampleModels\Sample;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\SampleOwned;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\SampleOwned2;

class RelationshipsTest extends TestCase
{
    public function test_belongsToOne_relationship(){
        $owner = jsonModelsFactory(Sample::class,$this->factory_path)->create();
        $owned = jsonModelsFactory(SampleOwned::class, $this->factory_path)->create([
            'sample_id' => $owner->id
        ]);
        $this->assertTrue($owned->owner()->first()->id == $owner->id);
    }

    public function test_belongsToOne_relationship_with_field_with_custom_name(){
        $owner = jsonModelsFactory(Sample::class,$this->factory_path)->create();
        $owned = jsonModelsFactory(SampleOwned2::class, $this->factory_path)->create([
            'owner' => $owner->id
        ]);
        $this->assertTrue($owned->owner()->first()->id == $owner->id);
    }

    public function test_hasMany_relationship(){
        $dummy = jsonModelsFactory(Sample::class, 5, $this->factory_path)->create()->shuffle();
        $owner = $dummy->extract()[0];
        $size = rand(1,10);
        $owned = jsonModelsFactory(SampleOwned::class, $size, $this->factory_path)->create([
            'sample_id' => $owner->id
        ]);
        $this->assertTrue(Sample::all()->count() > 0);
        $collection_owned = $owner->owned()->get();
        $this->assertTrue($collection_owned->count() == $size);
        foreach($collection_owned->extract() as $item){
            $this->assertTrue($item->sample_id == $owner->id);
        }
    }

    public function test_hasMany_relationship_with_field_with_custom_name(){
        $dummy = jsonModelsFactory(Sample::class, 5, $this->factory_path)->create();
        $owner = $dummy->extract()[0];
        $size = rand(1,10);
        $owned = jsonModelsFactory(SampleOwned2::class, $size, $this->factory_path)->create([
            'owner' => $owner->id
        ]);
        $this->assertTrue(Sample::all()->count() > 0);
        $collection_owned = $owner->owned2()->get();
        $this->assertTrue($collection_owned->count() == $size);
        foreach($collection_owned->extract() as $item){
            $this->assertTrue($item->owner == $owner->id);
        }
    }

    
}
