<?php

use guifcoelho\JsonModels\Model;
use guifcoelho\JsonModels\Factory;

if(!function_exists('jsonModelFactory')){
    function jsonModelFactory($class, ...$params){
        $args = func_get_args();
        $size = 1;
        $path = "";
        if(count($args) == 2){
            $size = is_int($args[1]) ? $args[1] : 1;
            $path = is_string($args[1]) ? $args[1] : "";
            if(!is_int($args[1]) && !is_string($args[1])){
                throw new \Exception("Second argument must be either integer or string");
            }
        }
        if(count($args) == 3){
            if(!is_int($args[1])){
                throw new \Exception("Second argument must be integer");
            }
            $size = $args[1];
            if(!is_string($args[2])){
                throw new \Exception("Third argument must be string");
            }
            $path = $args[2];
        }
        
        if(get_parent_class($class) == Model::class){
            return (new Factory($class, $size, $path))->load();
        }
        throw new \Exception("'{$class}' must be a subclass of '".Model::class."'");
    }
}