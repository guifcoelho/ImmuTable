<?php

namespace guifcoelho\JsonModels;

use Symfony\Component\Finder\Finder;
use guifcoelho\JsonModels\Model;
use guifcoelho\JsonModels\Collection;

class Factory{

    protected $definitions = [];

    protected $path;

    protected $faker;

    protected $class;

    protected $size;

    public function __construct($class, int $size = 1, string $path = ""){
        $this->class = $class;
        $this->size = $size;
        $this->path = function_exists('database_path') ? database_path('factories') : $path;
        $this->faker = \Faker\Factory::create();
    }

    /**
     * Loads the factory for the given model class
     *
     * @return void
     */
    public function load(){
        $factory = $this;
        if (is_dir($this->path)) {
            foreach (Finder::create()->files()->name('*Factory.php')->in($this->path) as $file) {
                require $file->getRealPath();
            }
        }
        return $factory;
    }

    /**
     * Sets the definition for new JsonModel factory.
     * 
     * It will not register if `$class` is not subclass of `\guifcoelho\JsonModels\Model`
     *
     * @param $class
     * @param callable $attributes
     * @return void
     */
    protected function define($class, callable $attributes){
        if(is_subclass_of($class, Model::class)){
            $this->definitions[$class] = $attributes;
        }
        return $this;
    }

    protected function buildData(array $attributes = []){
        if(!array_key_exists($this->class, $this->definitions)){
            throw new \Exception("No definitions set for class '{$this->class}'");
        }
        $collection = [];
        foreach(range(1, $this->size) as $item){
            $definitions = call_user_func($this->definitions[$this->class], $this->faker);
            foreach($attributes as $key => $value){
                $definitions[$key] = $value;
            }
            $collection[] = $definitions;
        }
        if($this->size == 1){
            return new $this->class($collection[0]);
        }
        return new Collection($this->class, $collection);
    }

    public function make(array $attributes = [])
    {
        $data_built = $this->buildData($attributes);
        /*
        | Run after making methods
        */
        return $data_built;
    }

    public function create(array $attributes = []){
        $data_built = $this->buildData($attributes);
        $data_built = is_subclass_of($data_built, Model::class) ? [$data_built] : $data_built;
        /*
        | Run after making methods
        */
        $models_created = (new Query($this->class))->insert($data_built);
        return $models_created;
    }

}