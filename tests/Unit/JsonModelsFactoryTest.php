<?php

namespace guifcoelho\JsonModels\Tests\Unit;

use guifcoelho\JsonModels\Tests\TestCase;
use guifcoelho\JsonModels\Tests\Unit\SampleModels\Sample as SampleModel;
use guifcoelho\JsonModels\Testing\TestingJsonModels;
use guifcoelho\JsonModels\Model;
use guifcoelho\JsonModels\Collection;

class JsonModelsFactoryTest extends TestCase
{
    use TestingJsonModels;

    public function test_making_one_json_model()
    {
        $model = jsonModelFactory(SampleModel::class, $this->factory_path)->make();
        $this->assertTrue(is_subclass_of($model, Model::class));
        $new_model = new SampleModel($model->toArray());
        $this->arrays()->assertSimilar($model->toArray(), $new_model->toArray());
    }

    public function test_making_many_json_models()
    {
        $coll = jsonModelFactory(SampleModel::class, 10, $this->factory_path)->make();
        $this->assertTrue($coll->count() == 10);
        $this->assertTrue(get_class($coll) == Collection::class);
        $new_coll = new Collection(SampleModel::class, $coll->toArray());
        $this->arrays()->assertSimilar($coll->toArray(), $new_coll->toArray());
    }

    public function test_creating_one_json_models()
    {
        $model = jsonModelFactory(SampleModel::class, $this->factory_path)->create();
        $this->jsontables()->assertTableHas(SampleModel::class, $model->toArray());
    }

    public function test_creating_many_json_models()
    {
        $collection = jsonModelFactory(SampleModel::class, 10, $this->factory_path)->create();
        $this->jsontables()->assertTableHas(SampleModel::class, $collection->toArray());
    }
}
