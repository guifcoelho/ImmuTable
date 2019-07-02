<?php

namespace guifcoelho\JsonModels\Tests\Unit;

use guifcoelho\JsonModels\Tests\TestCase;
use guifcoelho\JsonModels\Model;
use guifcoelho\JsonModels\Engine;
use guifcoelho\JsonModels\Exceptions\JsonModelsException;

class EngineClassTest extends TestCase
{
    public function test_construct_with_class_not_subclass_of_model(){
        try{
            $engine = new Engine('123');
        }catch(JsonModelsException $e){
            $this->assertTrue($e->getMessage() == "'123' is not a subclass of '".Model::class."'");
        }
    }    
}
