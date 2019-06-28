<?php

namespace guifcoelho\JsonModels\Tests\Unit;

use guifcoelho\JsonModels\Tests\TestCase;
use guifcoelho\JsonModels\Model;
use guifcoelho\JsonModels\Collection;
use guifcoelho\JsonModels\Exceptions\JsonModelsException;

use guifcoelho\JsonModels\Tests\Unit\SampleModels\Sample;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\Sample2;

class CollectionTest extends TestCase
{
    public function test_create_collection_with_not_subclass_of_JsonModel(){
        try{
            $dummy = new Collection('123', []);
        }catch(JsonModelsException $e){
            $this->assertTrue($e->getMessage() == "Model class must be a subclass of '".Model::class."'");
        }
    }

    public function test_create_collection_with_different_models(){
        $data = jsonModelsFactory(Sample::class, 10, $this->factory_path)->create();
        try{
            $dummy = new Collection(Sample2::class, $data->extract());
        }catch(JsonModelsException $e){
            $this->assertTrue($e->getMessage() == "Data must be either 'array' or '".Sample2::class."'");
        }
    }

    public function test_extracting_collection_into_json(){
        $data = jsonModelsFactory(Sample::class, 10, $this->factory_path)->create();
        $this->arrays()->assertSimilar($data->toArray(), json_decode($data->toJson(), true));
    }
}
