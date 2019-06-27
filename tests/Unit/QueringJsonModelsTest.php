<?php

namespace guifcoelho\JsonModels\Tests\Unit;

use guifcoelho\JsonModels\Tests\TestCase;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\Sample as SampleModel;
use guifcoelho\JsonModels\Testing\TestingJsonModels;

class CreateModelTest extends TestCase
{
    use TestingJsonModels;

    protected function eraseAndCreateNew(int $size){
        $data = [];
        foreach(range(1,$size) as $item){
            $data[] = [
                'id' => $item,
                'name' => "Name ".rand(0,100000),
                'email' => "email".rand(0,10000)."@email.com"
            ];
        }
        file_put_contents(SampleModel::getTablePath(), json_encode($data));
        return $data;
    }

    public function test_create_and_load_json_model()
    {
        $data = $this->eraseAndCreateNew(10);
        $model_data = SampleModel::all()->toArray();
        $this->arrays()->assertSimilar($data, $model_data);
    }

    public function test_query_json_model()
    {
        $data = $this->eraseAndCreateNew(10);
        $model_data = SampleModel::where('id', '>', 5)->get()->toArray();
        $filter_data = [];
        foreach($data as $item){
            if($item['id'] > 5){
                $filter_data[] = $item;
            }
        }
        $this->arrays()->assertSimilar($filter_data, $model_data);
    }

}
