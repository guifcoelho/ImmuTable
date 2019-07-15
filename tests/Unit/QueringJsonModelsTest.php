<?php

namespace guifcoelho\ImmuTable\Tests\Unit;

use guifcoelho\ImmuTable\Tests\TestCase;
use guifcoelho\ImmuTable\Exceptions\ImmuTableException;

use guifcoelho\ImmuTable\Query;
use guifcoelho\ImmuTable\Model;
use Illuminate\Support\Collection;
use guifcoelho\ImmuTable\Tests\Support\Assert\ImmuTableAssertions;

use guifcoelho\ImmuTable\Tests\Unit\SampleModels\Sample;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\Sample3;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\Sample4;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\SampleOwned;

class CreateModelTest extends TestCase
{
    use ImmuTableAssertions;

    public function test_create_and_load_json_model()
    {
        $data = ImmuTableFactory(Sample::class, 10, $this->factory_path)->create();
        $model_data = Sample::all();
        $this->assertTrue(get_class($model_data) == Collection::class);
        $this->assertSimilarArrays($data->toArray(), $model_data->toArray());
    }

    public function test_query_json_model()
    {
        $data = ImmuTableFactory(Sample::class, 10, $this->factory_path)->create();
        $model_data = Sample::where('id', '>', 5)->get();
        $this->assertTrue(get_class($model_data) == Collection::class);
        $filter_data = [];
        foreach($data as $item){
            if($item->id > 5){
                $filter_data[] = $item->toArray();
            }
        }
        $this->assertSimilarArrays($filter_data, $model_data->toArray());
    }

    public function test_querying_with_invalid_sign(){
        ImmuTableFactory(Sample::class, 10, $this->factory_path)->create();
        try{
            $model = Sample::where('id', 'das', 3)->get();
            $this->assertTrue(false);
        }catch(ImmuTableException $e){
            $this->assertTrue($e->getMessage() == "The second argument must be a valid comparison sign");
        }
    }

    public function test_querying_with_invalid_sign2(){
        ImmuTableFactory(Sample::class, 10, $this->factory_path)->create();
        try{
            $model = Sample::where('id', 'dasdas', 3);
            $this->assertTrue(false);
        }catch(ImmuTableException $e){
            $this->assertTrue($e->getMessage() == "The second argument must be a valid comparison sign");
        }
    }

    public function test_querying_with_invalid_value(){
        ImmuTableFactory(Sample::class, 10, $this->factory_path)->create();
        try{
            $model = Sample::where('id', '==', [1,2,3]);
            $this->assertTrue(false);
        }catch(ImmuTableException $e){
            $this->assertTrue($e->getMessage() == "The third argument must be either a number or a string");
        }
    }

    public function test_querying_empty_table(){
        $sample_table = (new Sample())->getTablePath();
        if(file_exists($sample_table)){
            unlink($sample_table);
        }
        $sample_owned_table = (new SampleOwned)->getTablePath();
        if(file_exists($sample_owned_table)){
            unlink($sample_owned_table);
        }
        $this->assertTrue(count(Sample::all()) == 0);
    }

    public function test_query_with_non_json_model_class(){
        try{
            $query = new Query(get_class($this));
            $this->assertTrue(false);
        }catch(ImmuTableException $e){
            $this->assertTrue($e->getMessage() == "'".get_class($this)."' must be a subclass of '".Model::class."'");
        }
    }

    public function test_inserting_with_object_different_than_array_or_collection(){
        $data = 12345;
        try{
            (new Query(Sample::class))->fill($data);
            $this->assertTrue(false);
        }catch(ImmuTableException $e){
            $this->assertTrue($e->getMessage() == "The data must be a subclass of '".Model::class."' or an instance of '".Collection::class."'");
        }
    }

    public function test_querying_first_null(){
        $dummy = ImmuTableFactory(Sample::class, 10, $this->factory_path)->create();
        $query = Sample::where('id', '<', 0)->first();
        $this->assertTrue($query == null);
    }

    public function test_querying_with_where_chaining(){
        $dummy = ImmuTableFactory(Sample::class, 10, $this->factory_path)->create();
        $ids = array_column($dummy->toArray(), 'id');
        $collection = Sample::where('id', '>=', $ids[0])
                            ->where('id', '<', max($ids)/2)
                            ->get();
        $this->assertTrue(count($collection) == 10/2-1);
    }

    public function test_querying_orWhere(){
        $dummy1 = ImmuTableFactory(Sample::class, $this->factory_path)->create();
        $dummy2 = ImmuTableFactory(Sample::class, $this->factory_path)->create();
        $collection = Sample::where('id', $dummy1->id);
        $collection = $collection->orWhere('id', $dummy2->id);
        $collection = $collection->get();
        foreach($collection as $item){
            $this->assertTrue($item->id == $dummy1->id || $item->id == $dummy2->id);
        }
    }

    public function test_exception_while_querying_with_fields_constraint(){
        try{
            $dummy = ImmuTableFactory(Sample3::class, 10, $this->factory_path)->create();
            $this->assertTrue(false);
        }catch(ImmuTableException $e){
            $this->assertTrue($e->getMessage() == "Field 'email' was not found in the data provided");
        }
    }

    public function test_querying_with_fields_constraint(){
        $data = ImmuTableFactory(Sample4::class, 10, $this->factory_path)->create();
        $this->assertImmuTableHas(Sample4::class, $data->toArray());
    }

    public function test_inserting_array_of_data(){
        try{
            $dummy = ImmuTableFactory(Sample::class, 10, $this->factory_path)->make();
            (new Query(Sample::class))->fill($dummy);
            $this->assertTrue(false);
        }
        catch(ImmuTableException $e){
            $this->assertTrue($e->getMessage() == "Primary key value not defined");
        }
    }
}
