<?php

namespace guifcoelho\ImmuTable\Tests\Unit;

use guifcoelho\ImmuTable\Tests\TestCase;

use guifcoelho\ImmuTable\Tests\Unit\SampleModels\Sample;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\SampleOwned;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\SampleOwned2;

class RelationshipsTest extends TestCase
{
    public function test_belongsTo_relationship(){
        $owner = ImmuTableFactory(Sample::class,$this->factory_path)->create();
        $owned = ImmuTableFactory(SampleOwned::class, $this->factory_path)->create([
            'sample_id' => $owner->id
        ]);
        $this->assertTrue($owned->owner()->id == $owner->id);
    }

    public function test_belongsTo_relationship_with_field_with_custom_name(){
        $owner = ImmuTableFactory(Sample::class,$this->factory_path)->create();
        $owned = ImmuTableFactory(SampleOwned2::class, $this->factory_path)->create([
            'owner' => $owner->id
        ]);
        $this->assertTrue($owned->owner()->id == $owner->id);
    }

    public function test_hasMany_relationship(){
        $dummy = ImmuTableFactory(Sample::class, 5, $this->factory_path)->create()->shuffle();
        $owner = $dummy[0];
        $size = rand(1,10);
        $owned = ImmuTableFactory(SampleOwned::class, $size, $this->factory_path)->create([
            'sample_id' => $owner->id
        ]);
        $this->assertTrue(count(Sample::all()) > 0);
        $collection_owned = $owner->owned();
        $this->assertTrue(count($collection_owned) == $size);
        foreach($collection_owned as $item){
            $this->assertTrue($item->sample_id == $owner->id);
        }
    }

    public function test_hasMany_relationship_with_field_with_custom_name(){
        $dummy = ImmuTableFactory(Sample::class, 5, $this->factory_path)->create();
        $owner = $dummy[0];
        $size = rand(1,10);
        $owned = ImmuTableFactory(SampleOwned2::class, $size, $this->factory_path)->create([
            'owner' => $owner->id
        ]);
        $this->assertTrue(count(Sample::all()) > 0);
        $collection_owned = $owner->owned2();
        $this->assertTrue(count($collection_owned) == $size);
        foreach($collection_owned as $item){
            $this->assertTrue($item->owner == $owner->id);
        }
    }
}
