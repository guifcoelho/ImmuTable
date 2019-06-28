<?php

namespace guifcoelho\JsonModels;

use guifcoelho\JsonModels\Exceptions\JsonModelsException;
use guifcoelho\JsonModels\Model;

class Collection{

    protected $model_class;

    protected $collection;

    /**
     * Creates a JsonModelCollection object
     *
     * @param string $class
     * @param array $data
     */
    public function __construct(string $class, array $data = []){
        if(!is_subclass_of($class, Model::class)){
            throw new JsonModelsException("Model class must be a subclass of '".Model::class."'");
        }
        $this->model_class = $class;
        $this->collection = $this->loadData($data);
    }

    protected function loadData(array $data){
        $self = $this;        
        return array_map(function($el) use($self){
            if(is_array($el)){
                return new $self->model_class($el);
            }elseif(get_class($el) == $self->model_class){
                return $el;
            }else{
                throw new JsonModelsException("Data must be either 'array' or '{$self->model_class}'");
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
     * Extracts collection into array
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


    public function count():int
    {
        if($this->collection == null){
            return 0;
        }
        return count($this->collection);
    }

    public function extract(){
        return $this->collection;
    }
}
