<?php

namespace guifcoelho\ImmuTable\Tests\Unit;

use guifcoelho\ImmuTable\Tests\TestCase;
use guifcoelho\ImmuTable\Exceptions\ImmuTableException;

use guifcoelho\ImmuTable\Model;
use Illuminate\Support\Collection;

use guifcoelho\ImmuTable\Tests\Unit\SampleModels\Sample as SampleModel;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\SampleWithoutFactory;

class ImmuTableFactoryTest extends TestCase
{
    use \guifcoelho\ImmuTable\Testing\Support\ImmuTableAssertions;
    
    public function test_making_one_json_model()
    {
        $model = ImmuTableFactory(SampleModel::class, $this->factory_path)->make();
        $this->assertTrue(get_class($model) == SampleModel::class);
        $new_model = new SampleModel($model->toArray());
        $this->assertSimilarArrays($model->toArray(), $new_model->toArray());
    }

    public function test_making_many_json_models()
    {
        $coll = ImmuTableFactory(SampleModel::class, 10, $this->factory_path)->make();
        $this->assertTrue(count($coll) == 10);
        $this->assertTrue(get_class($coll) == Collection::class);
        foreach($coll as $item){
            $this->assertTrue(get_class($item) == SampleModel::class);
        }
    }

    public function test_creating_one_json_models()
    {
        $model = ImmuTableFactory(SampleModel::class, $this->factory_path)->create();
        $this->assertTrue(get_class($model) == SampleModel::class);
        $this->assertImmuTableHas(SampleModel::class, $model->toArray());
    }

    public function test_creating_many_json_models()
    {
        $collection = ImmuTableFactory(SampleModel::class, 10, $this->factory_path)->create();
        $this->assertTrue(get_class($collection) == Collection::class);
        foreach($collection as $item){
            $this->assertTrue(get_class($item) == SampleModel::class);
        }
        $this->assertImmuTableHas(SampleModel::class, $collection->toArray());
    }

    public function test_for_model_without_factory(){
        try{
            $dummy = ImmuTableFactory(SampleWithoutFactory::class, $this->factory_path)->create();
            $this->assertTrue(false);
        }catch(ImmuTableException $e){
            $this->assertTrue($e->getMessage() == "No definitions set for class '".SampleWithoutFactory::class."'");
        }
    }

    public function test_factory_function_with_non_json_model_class(){
        try{
            $dummy = ImmuTableFactory('123');
            $this->assertTrue(false);
        }catch(ImmuTableException $e){
            $this->assertTrue($e->getMessage() == "The model class must be a subclass of '".Model::class."'");
        }
    }

    public function test_factory_function_invalid_second_argument_with_only_two_arguments(){
        try{
            $dummy = ImmuTableFactory(SampleModel::class, []);
            $this->assertTrue(false);
        }catch(ImmuTableException $e){
            $this->assertTrue($e->getMessage() == "Second argument must either be the size of collection or the path to factories");
        }
    }

    public function test_factory_function_invalid_second_and_third_arguments(){
        try{
            $dummy = ImmuTableFactory(SampleModel::class, "1", 1);
            $this->assertTrue(false);
        }catch(ImmuTableException $e){
            $this->assertTrue($e->getMessage() == "Second argument must be integer and third argument must be string");
        }
    }

    public function test_factory_function_invalid_second_argument_with_three_arguments(){
        try{
            $dummy = ImmuTableFactory(SampleModel::class, "1", $this->factory_path);
            $this->assertTrue(false);
        }catch(ImmuTableException $e){
            $this->assertTrue($e->getMessage() == "Second argument must be integer and third argument must be string");
        }
    }

    public function test_factory_function_invalid_third_argument_with_three_arguments(){
        try{
            $dummy = ImmuTableFactory(SampleModel::class, 1, []);
            $this->assertTrue(false);
        }catch(ImmuTableException $e){
            $this->assertTrue($e->getMessage() == "Second argument must be integer and third argument must be string");
        }
    }
}
