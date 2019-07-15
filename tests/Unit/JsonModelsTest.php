<?php

namespace guifcoelho\ImmuTable\Tests\Unit;

use guifcoelho\ImmuTable\Tests\TestCase;
use guifcoelho\ImmuTable\Tests\Support\Assert\ArrayAssertions;

use guifcoelho\ImmuTable\Model;
use Illuminate\Support\Collection;

use guifcoelho\ImmuTable\Tests\Unit\SampleModels\Sample as SampleModel;

class ImmuTableTest extends TestCase
{
    use ArrayAssertions;
    
    public function test_transforming_json_models_to_array(){
        $model = ImmuTableFactory(SampleModel::class, $this->factory_path)->create();
        $this->assertTrue(is_array($model->toArray()));
        $data = [];
        foreach($model->getFields() as $field){
            $data[$field] = $model->$field;
        }
        $this->assertSimilarArrays($model->toArray(), $data);
    }
}
