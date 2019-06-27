<?php

namespace guifcoelho\JsonModels\Tests\Unit;

use guifcoelho\JsonModels\Tests\TestCase;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\Sample as SampleModel;
use guifcoelho\JsonModels\Testing\TestingJsonModels;

class CreateModelTest extends TestCase
{
    use TestingJsonModels;

    public function test_create_and_load_json_model()
    {
        $data = jsonModelFactory(SampleModel::class, 10, $this->factory_path)->create();
        file_put_contents(SampleModel::getTablePath(), json_encode($data->toArray()));
        $model_data = SampleModel::all()->toArray();
        $this->arrays()->assertSimilar($data->toArray(), $model_data);
    }

    public function test_query_json_model()
    {
        $data = jsonModelFactory(SampleModel::class, 10, $this->factory_path)->create();
        file_put_contents(SampleModel::getTablePath(), json_encode($data->toArray()));
        $model_data = SampleModel::where('id', '>', 5)->get()->toArray();
        $filter_data = [];
        foreach($data->extract() as $item){
            if($item->id > 5){
                $filter_data[] = $item->toArray();
            }
        }
        $this->arrays()->assertSimilar($filter_data, $model_data);
    }

    public function test_querying_with_invalid_arguments(){
        jsonModelFactory(SampleModel::class, 10, $this->factory_path)->create();
        try{
            $model = SampleModel::where('id', 123, 3);
        }catch(\Exception $e){
            $this->assertTrue($e->getMessage() == "The second argument must be a comparison sign");
        }

        try{
            $model = SampleModel::where('id', 'das', 3);
        }catch(\Exception $e){
            $this->assertTrue($e->getMessage() == "The second argument must be a comparison sign");
        }

        try{
            $model = SampleModel::where('id', '==', [1,2,3]);
        }catch(\Exception $e){
            $this->assertTrue($e->getMessage() == "The third argument must be either a number or a string");
        }
    }

}
