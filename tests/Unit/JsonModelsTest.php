<?php

namespace guifcoelho\JsonModels\Tests\Unit;

use guifcoelho\JsonModels\Tests\TestCase;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\Sample as SampleModel;
use guifcoelho\JsonModels\Testing\TestingJsonModels;
use guifcoelho\JsonModels\Model;
use guifcoelho\JsonModels\Collection;

class JsonModelsTest extends TestCase
{
    use TestingJsonModels;

    public function test_transforming_json_models_to_array(){
        $model = jsonModelFactory(SampleModel::class, $this->factory_path)->create();
        $this->assertTrue(is_array($model->toArray()));
        $data = [];
        foreach($model->getFields() as $field){
            $data[$field] = $model->$field;
        }
        $this->arrays()->assertSimilar($model->toArray(), $data);
    }

    public function test_transforming_json_models_to_json(){
        $model = jsonModelFactory(SampleModel::class, $this->factory_path)->create();
        $this->assertTrue(is_string($model->toJson()));
        $data = json_decode($model->toJson(), true);
        $this->arrays()->assertSimilar($model->toArray(), $data);
    }
}
