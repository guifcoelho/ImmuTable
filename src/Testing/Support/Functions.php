<?php

use guifcoelho\JsonModels\Model;
use guifcoelho\JsonModels\Factory;
use guifcoelho\JsonModels\Config;
use guifcoelho\JsonModels\Exceptions\JsonModelsException;

if(!function_exists('jsonModelsFactory')){

    /**
     * Run factory build json models. It should be used solely for testing or prototyping
     *
     * @param string $class
     * @param  ...$params
     */
    function jsonModelsFactory(string $class, ...$params){
        $size = 1;
        $path = "";
        if(count($params) == 1){
            $size = is_int($params[0]) ? $params[0] : 1;
            $path = is_string($params[0]) ? $params[0] : "";
            if(!is_int($params[0]) && !is_string($params[0])){
                throw new JsonModelsException("Second argument must either be the size of collection or the path to factories");
            }
        }
        if(count($params) == 2){
            if(!is_int($params[0]) || !is_string($params[1])){
                throw new JsonModelsException("Second argument must be integer and third argument must be string");
            }
            $size = $params[0];
            $path = $params[1];
        }
        
        if(!is_subclass_of($class, Model::class)){
            throw new JsonModelsException("The model class must be a subclass of '".Model::class."'");
        }
        return (new Factory($class, $size, $path))->load();
    }
}