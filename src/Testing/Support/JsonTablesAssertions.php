<?php

namespace guifcoelho\JsonModels\Testing\Support;

use PHPUnit_Framework_Assert as PHPUnit;
use guifcoelho\JsonModels\Testing\Support\ArrayAssertions;
use guifcoelho\JsonModels\Model;

class JsonTablesAssertions{

    public function assertTableHas($class, array $expected){
        PHPUnit::assertTrue(
            is_subclass_of($class, Model::class),
            "'{$class}' is not a subclass of '".Model::class."'"
        );
        $primary_key = $class::getPrimaryKey();
        $query = [];
        if(array_key_exists($primary_key, $expected)){
            $query = $class::where($primary_key, $expected[$primary_key])->first()->toArray();
        }else{
            foreach($expected as $item){
                $query[] = $class::where($primary_key, $item[$primary_key])->first()->toArray();
            }
        }
        return ArrayAssertions::assertSimilar($expected, $query);
    }

}