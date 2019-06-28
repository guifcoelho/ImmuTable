<?php

namespace guifcoelho\JsonModels;

use guifcoelho\JsonModels\Exceptions\JsonModelsException;
use guifcoelho\JsonModels\Model;

class Collection{

    /**
     * JsonModel class name
     *
     * @var string
     */
    protected $class = "";

    /**
     * The collection array
     *
     * @var array
     */
    protected $collection = [];

    /**
     * Creates the Collection object of JsonModels
     *
     * @param string $class
     * @param array $data
     */
    public function __construct(string $class, array $data = []){
        if(!is_subclass_of($class, Model::class)){
            throw new JsonModelsException("Model class must be a subclass of '".Model::class."'");
        }
        $this->class = $class;
        $this->collection = $this->loadData($data);
    }

    /**
     * Loads the provided data
     *
     * @param array $data
     */
    protected function loadData(array $data){
        $self = $this;        
        return array_map(function($el) use($self){
            if(is_array($el)){
                return new $self->class($el);
            }elseif(get_class($el) == $self->class){
                return $el;
            }else{
                throw new JsonModelsException("Data must be either 'array' or '{$self->class}'");
            }
        }, $data);
    }

    /**
     * Shuffles the collection
     *
     * @return self
     */
    public function shuffle():self
    {
        shuffle($this->collection);
        return $this;
    }

    /**
     * Extracts recursively the collection into array
     *
     * @return array
     */
    public function toArray():array
    {
        $collection = $this->collection;
        return array_map(function($item){
            return $item->toArray();
        }, $collection);
    }

    /**
     * Extracts the collection into json
     *
     * @return string
     */
    public function toJson():string
    {
        return json_encode($this->toArray());
    }

    /**
     * Counts the number of JsonModels inside the collection
     *
     * @return integer
     */
    public function count():int
    {
        if($this->collection == null){
            return 0;
        }
        return count($this->collection);
    }

    /**
     * Extracts the collection of JsonModels
     *
     * @return array
     */
    public function extract():array{
        return $this->collection;
    }
}
