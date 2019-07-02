<?php

namespace guifcoelho\ImmuTable\Tests\Unit;

use guifcoelho\ImmuTable\Tests\TestCase;
use guifcoelho\ImmuTable\Model;
use guifcoelho\ImmuTable\Engine;
use guifcoelho\ImmuTable\Exceptions\ImmuTableException;

class EngineClassTest extends TestCase
{
    public function test_construct_with_class_not_subclass_of_model(){
        try{
            $engine = new Engine('123');
            $this->assertTrue(false);
        }catch(ImmuTableException $e){
            $this->assertTrue($e->getMessage() == "'123' is not a subclass of '".Model::class."'");
        }
    }    
}
