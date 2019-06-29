<?php

namespace guifcoelho\JsonModels\Tests\Unit;

use guifcoelho\JsonModels\Tests\TestCase;
use guifcoelho\JsonModels\Exceptions\JsonModelsException;

use guifcoelho\JsonModels\Model;
use guifcoelho\JsonModels\Collection;

use guifcoelho\JsonModels\Tests\Unit\SampleModels\Sample as SampleModel;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\SampleWithoutFactory;

class JsonModelsFactoryTest extends TestCase
{
    use \guifcoelho\JsonModels\Testing\Support\JsonTablesAssertions;
    
    public function test_making_one_json_model()
    {
        $model = jsonModelsFactory(SampleModel::class, $this->factory_path)->make();
        $this->assertTrue(is_subclass_of($model, Model::class));
        $new_model = new SampleModel($model->toArray());
        $this->assertSimilarArrays($model->toArray(), $new_model->toArray());
    }

    public function test_making_many_json_models()
    {
        $coll = jsonModelsFactory(SampleModel::class, 10, $this->factory_path)->make();
        $this->assertTrue(count($coll) == 10);
        $this->assertTrue(get_class($coll) == Collection::class);
        $new_coll = new Collection(SampleModel::class, $coll->toArray());
        $this->assertSimilarArrays($coll->toArray(), $new_coll->toArray());
    }

    public function test_creating_one_json_models()
    {
        $model = jsonModelsFactory(SampleModel::class, $this->factory_path)->create();
        $this->assertJsonTableHas(SampleModel::class, $model->toArray());
    }

    public function test_creating_many_json_models()
    {
        $collection = jsonModelsFactory(SampleModel::class, 10, $this->factory_path)->create();
        $this->assertJsonTableHas(SampleModel::class, $collection->toArray());
    }

    public function test_for_model_without_factory(){
        try{
            $dummy = jsonModelsFactory(SampleWithoutFactory::class, $this->factory_path)->create();
        }catch(JsonModelsException $e){
            $this->assertTrue($e->getMessage() == "No definitions set for class '".SampleWithoutFactory::class."'");
        }
    }

    public function test_factory_function_with_non_json_model_class(){
        try{
            $dummy = jsonModelsFactory('123');
        }catch(JsonModelsException $e){
            $this->assertTrue($e->getMessage() == "The model class must be a subclass of '".Model::class."'");
        }
    }

    public function test_factory_function_invalid_second_argument_with_only_two_arguments(){
        try{
            $dummy = jsonModelsFactory(SampleModel::class, []);
        }catch(JsonModelsException $e){
            $this->assertTrue($e->getMessage() == "Second argument must either be the size of collection or the path to factories");
        }
    }

    public function test_factory_function_invalid_second_and_third_arguments(){
        try{
            $dummy = jsonModelsFactory(SampleModel::class, "1", 1);
        }catch(JsonModelsException $e){
            $this->assertTrue($e->getMessage() == "Second argument must be integer and third argument must be string");
        }
    }

    public function test_factory_function_invalid_second_argument_with_three_arguments(){
        try{
            $dummy = jsonModelsFactory(SampleModel::class, "1", $this->factory_path);
        }catch(JsonModelsException $e){
            $this->assertTrue($e->getMessage() == "Second argument must be integer and third argument must be string");
        }
    }

    public function test_factory_function_invalid_third_argument_with_three_arguments(){
        try{
            $dummy = jsonModelsFactory(SampleModel::class, 1, []);
        }catch(JsonModelsException $e){
            $this->assertTrue($e->getMessage() == "Second argument must be integer and third argument must be string");
        }
    }
    
}
