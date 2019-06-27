<?php

namespace guifcoelho\JsonModels;

use JsonMachine\JsonMachine;
use guifcoelho\JsonModels\Model;
use guifcoelho\JsonModels\Collection;

class Query{

    protected $model_class;

    protected $queried;

    /**
     * Creates a Query object
     *
     * @param array $data
     */
    public function __construct($class){
        if(!is_subclass_of($class, Model::class)){
            throw new \Exception("'{$class}' must be a subclass of '".Model::class."'");
        }
        $this->model_class = $class;
    }

    /**
     * Loads table data JsonMachine iterable
     */
    private function loadTable(bool $as_stream = true)
    {
        $table_path = $this->model_class::getTablePath();
        if(!file_exists($table_path)){
            return [];
        }
        if($as_stream){
            return JsonMachine::fromFile($table_path);
        }else{
            return json_decode(file_get_contents($table_path), true);
        }
    }

    /**
     * Evaluates a JsonModel object according to its field, comparison sign and value searched
     *
     * @param mixed $el
     * @param string $sign
     * @param mixed $value
     * @return boolean
     */
    private function evalModelItem($el, string $sign, $value):bool
    {
        switch($sign){
            case '<': return $el < $value;
            case '>': return $el > $value;
            case '<=': return $el <= $value;
            case '>=': return $el >= $value;
            case '==': return $el == $value;
            case '<=': return $el <= $value;
            case '>=': return $el >= $value;
            case '===': return $el === $value;
            default: throw new \Exception("The second argument must be a comparison sign");
        }
    }

    /**
     * Return list of primary keys queried
     *
     * @return array
     */
    public function getQueried():array
    {
        return $this->queried;
    }

    /**
     * Querys the json table
     *
     * @param $model_class
     * @param string $campo
     * @param string $comparacao
     * @param $valor
     * @return void
     */
    public function where(string $field, string $sign, $value):self
    {
        $data = $this->loadTable();
        $query = [];
        foreach($data as $item){
            if($this->evalModelItem($item[$field], $sign, $value)){
                $query[] = $item[$this->model_class::getPrimaryKey()];
            }
        }
        $this->queried = $query;
        return $this;
    }  
    

    public function orWhere(string $field, string $sign, $value):self
    {
        $query = $this->model_class::where($field, $sign, $value)->getQueried();
        $this->queried = array_unique(array_merge($this->queried, $query));
        return $this;
    }

    /**
     * Returns the first item of the collection
     *
     * @return void
     */
    public function first(){
        $data = $this->loadTable();
        $primary_key = $this->model_class::getPrimaryKey();
        foreach($data as $item){
            if($item[$primary_key] == $this->queried[0]){
                return new $this->model_class($item);
            }
        }
    }

    

    /**
     * Returns all data inside table
     *
     * @return self
     */
    public function all()
    {
        $data = $this->loadTable(false);
        $this->queried = array_values(array_column($data, $this->model_class::getPrimaryKey()));
        return count($data) == 0 ? null : new Collection($this->model_class, $data);
    }

    /**
     * Gets the queried collection
     *
     * @return self
     */
    public function get()
    {
        $queried = $this->queried;
        $collection = [];
        if(count($queried) > 0){
            $data = $this->loadTable();
            $primary_key = $this->model_class::getPrimaryKey();
            foreach($data as $item){
                $item_primary_key_value = $item[$primary_key];
                if(array_search($item_primary_key_value, $queried) !== false){
                    $collection[] = $item;
                    $queried = array_filter($queried, function($el) use($item_primary_key_value){
                        return $el != $item_primary_key_value;
                    });
                }
            }
            
        }
        return count($collection) == 0 ? null : new Collection($this->model_class, $collection);
    }

    public function getLastPrimaryKeyValue():int
    {
        $data = $this->loadTable();
        $last_primary_key_value = 0;
        foreach($data as $item){
            $last_primary_key_value = max($last_primary_key_value, $item[$this->model_class::getPrimaryKey()]);
        }
        return $last_primary_key_value;
    }

    public function insert($data)
    {
        if(!is_array($data) && is_subclass_of($data, Collection::class)){
            throw new Exception("Data to be inserted must array or subclass of '".Collection::class."'");
        }
        $current = $this->loadTable();
        $collection = [];
        foreach($current as $item){
            $collection[] = new $this->model_class($item);
        }
        $last_primary_key_value = $this->getLastPrimaryKeyValue();
        $primary_key = $this->model_class::getPrimaryKey();

        if(is_array($data)){
            $data = (new Collection($this->model_class, $data))->extract();
        }else{
            $data = $data->extract();
        }
        foreach($data as &$item){
            $item = $item->toArray();
            $item[$primary_key] = ++$last_primary_key_value;
            $item = new $this->model_class($item);
            $collection[] = $item;
        }
        $collection = (new Collection($this->model_class, $collection));
        file_put_contents($this->model_class::getTablePath(), json_encode($collection->toArray()));

        $models_inserted = new Collection($this->model_class, $data);
        return $models_inserted->count() == 1 ? $models_inserted->extract()[0] : $models_inserted;
    }
}
