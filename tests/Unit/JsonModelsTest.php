<?php

namespace guifcoelho\JsonModels\Tests\Unit;

use guifcoelho\JsonModels\Tests\TestCase;

use guifcoelho\JsonModels\Model;
use Illuminate\Support\Collection;

use guifcoelho\JsonModels\Tests\Unit\SampleModels\Sample as SampleModel;

class JsonModelsTest extends TestCase
{
    use \guifcoelho\JsonModels\Testing\Support\ArrayAssertions;
    
    public function test_transforming_json_models_to_array(){
        $model = jsonModelsFactory(SampleModel::class, $this->factory_path)->create();
        $this->assertTrue(is_array($model->toArray()));
        $data = [];
        foreach($model->getFields() as $field){
            $data[$field] = $model->$field;
        }
        $this->assertSimilarArrays($model->toArray(), $data);
    }
}
