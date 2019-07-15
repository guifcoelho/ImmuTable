<?php

namespace guifcoelho\ImmuTable\Tests\Unit;

use guifcoelho\ImmuTable\Tests\TestCase;
use guifcoelho\ImmuTable\Tests\Support\TestingDatabase;
use guifcoelho\ImmuTable\Tests\Support\Assert\ArrayAssertions;
use guifcoelho\ImmuTable\Relations\Pivot;

use guifcoelho\ImmuTable\Tests\Unit\SampleModels\Sample;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\Sample2;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\SampleOwned;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\SampleOwned2;

class RelationshipsTest extends TestCase
{
    use ArrayAssertions, TestingDatabase;
    
    public function test_belongsTo_relationship(){
        $owner = ImmuTableFactory(Sample::class,$this->factory_path)->create();
        $owned = ImmuTableFactory(SampleOwned::class, $this->factory_path)->create([
            'sample_id' => $owner->id
        ]);
        $this->assertTrue($owned->owner()->first()->id == $owner->id);
    }

    public function test_belongsTo_relationship_with_field_with_custom_name(){
        $owner = ImmuTableFactory(Sample::class,$this->factory_path)->create();
        $owned = ImmuTableFactory(SampleOwned2::class, $this->factory_path)->create([
            'owner' => $owner->id
        ]);
        $this->assertTrue($owned->owner()->first()->id == $owner->id);
    }

    public function test_hasMany_relationship(){
        $dummy = ImmuTableFactory(Sample::class, 5, $this->factory_path)->create()->shuffle();
        $owner = $dummy[0];
        $size = rand(1,10);
        $owned = ImmuTableFactory(SampleOwned::class, $size, $this->factory_path)->create([
            'sample_id' => $owner->id
        ]);
        $this->assertTrue(count(Sample::all()) > 0);
        $collection_owned = $owner->owned()->get();
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
        $collection_owned = $owner->owned2()->get();
        $this->assertTrue(count($collection_owned) == $size);
        foreach($collection_owned as $item){
            $this->assertTrue($item->owner == $owner->id);
        }
    }

    public function test_hasOne_relationship(){
        $dummy = ImmuTableFactory(Sample2::class, 5, $this->factory_path)->create();
        $owner = $dummy[0];
        $size = rand(1,10);
        $owned = ImmuTableFactory(SampleOwned::class, $size, $this->factory_path)->create([
            'sample2_id' => $owner->id
        ]);
        $this->assertFalse(\method_exists($owner->owned(), 'get'));
        $this->assertSimilarArrays($owner->owned()->first()->toArray(), $owned->first()->toArray());
    }

    public function test_belongsToMany_relationship_detach_one(){
        $sample = ImmuTableFactory(Sample::class, $this->factory_path)->create();
        $sample->many_owners()->detach();
        $sample2 = ImmuTableFactory(Sample2::class, $this->factory_path)->create();
        
        $relation = $sample->many_owners()->save($sample2)->detach($sample2->id);
        $this->assertDatabaseHasNot($relation->getPivot()->getTable(), [
            'sample_id' => $sample->id,
            'sample2_id' => $sample2->id
        ]);
    }

    public function test_belongsToMany_relationship_detach_all(){
        $sample = ImmuTableFactory(Sample::class, $this->factory_path)->create();
        $sample->many_owners()->detach();
        $sample2_coll = ImmuTableFactory(Sample2::class, 5, $this->factory_path)->create();
        foreach($sample2_coll as $item){
            $relation = $sample->many_owners()->attach($item->id);
            $this->assertDatabaseHas($relation->getPivot()->getTable(), [
                'sample_id' => $sample->id,
                'sample2_id' => $item->id
            ]);
        }
        $relation = $sample->many_owners()->detach();
        $pivot = (new Pivot())->setTable($relation->getPivot()->getTable())->fillable(['sample_id', 'sample2_id']);
        $query = $pivot->builder()->where('sample_id', $sample->id)->get();
        $this->assertTrue(count($query) == 0);
    }

    public function test_belongsToMany_relationship_detach_many(){
        $sample = ImmuTableFactory(Sample::class, $this->factory_path)->create();
        $relation = $sample->many_owners()->detach();
        $dummy = ImmuTableFactory(Sample2::class, 10, $this->factory_path)->create();
        foreach($dummy as $item){
            $relation = $sample->many_owners()->attach($item->id);
            $this->assertDatabaseHas($relation->getPivot()->getTable(), [
                'sample_id' => $sample->id,
                'sample2_id' => $item->id
            ]);
        }
        $sample2_coll = ImmuTableFactory(Sample2::class, 5, $this->factory_path)->create();
        foreach($sample2_coll as $item){
            $relation = $sample->many_owners()->attach($item->id);
            $this->assertDatabaseHas($relation->getPivot()->getTable(), [
                'sample_id' => $sample->id,
                'sample2_id' => $item->id
            ]);
        }
        $relation = $sample->many_owners()->detach(array_column($dummy->toArray(), 'id'));
        $pivot = (new Pivot())->setTable($relation->getPivot()->getTable())->fillable(['sample_id', 'sample2_id']);
        $query = $pivot->builder()->where('sample_id', $sample->id)->get();
        $this->assertSimilarArrays(array_column($query->toArray(), 'sample2_id'), array_column($sample2_coll->toArray(), 'id'));
    }

    public function test_belongsToMany_relationship_sync_to_none(){
        $sample = ImmuTableFactory(Sample::class, $this->factory_path)->create();
        $relation = $sample->many_owners()->detach();
        $dummy = ImmuTableFactory(Sample2::class, 10, $this->factory_path)->create();
        foreach($dummy as $item){
            $relation = $sample->many_owners()->attach($item->id);
            $this->assertDatabaseHas($relation->getPivot()->getTable(), [
                'sample_id' => $sample->id,
                'sample2_id' => $item->id
            ]);
        }
        $relation = $sample->many_owners()->sync();
        $pivot = (new Pivot())->setTable($relation->getPivot()->getTable())->fillable(['sample_id', 'sample2_id']);
        $query = $pivot->builder()->where('sample_id', $sample->id)->get();
        $this->assertTrue(count($query) == 0);
    }

    public function test_belongsToMany_relationship_sync_to_some(){
        $sample = ImmuTableFactory(Sample::class, $this->factory_path)->create();
        $relation = $sample->many_owners()->detach();
        $dummy = ImmuTableFactory(Sample2::class, 10, $this->factory_path)->create();
        foreach($dummy as $item){
            $relation = $sample->many_owners()->attach($item->id);
            $this->assertDatabaseHas($relation->getPivot()->getTable(), [
                'sample_id' => $sample->id,
                'sample2_id' => $item->id
            ]);
        }
        $dummy = $dummy->shuffle();
        $chosen = [];
        foreach(range(1,3) as $i){
            $chosen[] = $dummy[$i]->toArray();
        }

        $relation = $sample->many_owners()->sync(array_column($chosen, 'id'));
        $pivot = (new Pivot())->setTable($relation->getPivot()->getTable())->fillable(['sample_id', 'sample2_id']);
        $query = $pivot->builder()->where('sample_id', $sample->id)->get();
        $this->assertTrue(count($query) == 3);
        foreach($chosen as $item){
            $this->assertDatabaseHas($relation->getPivot()->getTable(), [
                'sample_id' => $sample->id,
                'sample2_id' => $item['id']
            ]);
        }
    }

    public function test_belongsToMany_relationship_sync_to_one(){
        $sample = ImmuTableFactory(Sample::class, $this->factory_path)->create();
        $relation = $sample->many_owners()->detach();
        $dummy = ImmuTableFactory(Sample2::class, 10, $this->factory_path)->create();
        foreach($dummy as $item){
            $relation = $sample->many_owners()->attach($item->id);
            $this->assertDatabaseHas($relation->getPivot()->getTable(), [
                'sample_id' => $sample->id,
                'sample2_id' => $item->id
            ]);
        }
        $dummy = $dummy->shuffle();

        $relation = $sample->many_owners()->sync($dummy->first()->id);
        $pivot = (new Pivot())->setTable($relation->getPivot()->getTable())->fillable(['sample_id', 'sample2_id']);
        $query = $pivot->builder()->where('sample_id', $sample->id)->get();
        $this->assertTrue(count($query) == 1);
        $this->assertDatabaseHas($relation->getPivot()->getTable(), [
            'sample_id' => $sample->id,
            'sample2_id' => $dummy->first()->id
        ]);
    }
}
