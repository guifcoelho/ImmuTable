<?php

namespace guifcoelho\JsonModels;

use JsonMachine\JsonMachine;
use guifcoelho\JsonModels\Model;
use guifcoelho\JsonModels\Engine;
use Illuminate\Support\Collection;
use guifcoelho\JsonModels\Exceptions\JsonModelsException;

class Query{

    protected $class = '';

    protected $queried = [];

    /**
     * Instanciates a Query object
     *
     * @param array $data
     */
    public function __construct(string $class){
        if(!is_subclass_of($class, Model::class)){
            throw new JsonModelsException("'{$class}' must be a subclass of '".Model::class."'");
        }
        $this->class = $class;
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
            case '=':
            case '==': return $el == $value;
            case '<': return $el < $value;
            case '>': return $el > $value;
            case '<=': return $el <= $value;
            case '>=': return $el >= $value;
            case '===': return $el === $value;
            default: throw new JsonModelsException("The second argument must be a valid comparison sign");
        }
    }

    /**
     * Support function. Gets the query arguments
     *
     * @param array $args
     * @return array
     */
    public static function getQueryArguments(array $args):array
    {
        $sign = "==";
        if(count($args) == 1){
            $value = $args[0];
        }
        if(count($args) == 2){
            if(!is_string($args[0]) || strlen($args[0]) > 3){
                throw new JsonModelsException("The second argument must be a valid comparison sign");
            }
            $sign = $args[0];
            if(!is_numeric($args[1]) && !is_string($args[1])){
                throw new JsonModelsException("The third argument must be either a number or a string");
            }
            $value = $args[1];
        }
        return [
            'sign' => $sign,
            'value' => $value
        ];
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
     * Queries the JsonModel table
     *
     * @param string $field
     * @param ...$params Must provide comparison sign and value (sign is optional)
     * @return self
     */
    public function where(string $field, ...$params):self
    {
        $engine = new Engine($this->class);
        $args = static::getQueryArguments($params);
        $primary_key_name = $this->class::getPrimaryKey();
        if(count($this->queried) == 0){
            $self = $this;
            $data = $engine->filter(
                function($item) use($self, $field, $args){
                    return $self->evalModelItem($item->$field, $args['sign'], $args['value']);
                }
            );
            $data = $data->toArray();
            $query = array_values(array_map(function($item) use($primary_key_name){
                return $item[$primary_key_name];
            }, $data));

            $this->queried = $query;
        }else{
            $collection = $this->get();
            $query = [];
            foreach($collection as $item){
                if($this->evalModelItem($item->$field, $args['sign'], $args['value'])){
                    $query[] = $item->$primary_key_name;
                }
            }
            $this->queried = $query;

        }
        return $this;
    }  
    
    /**
     * Queries the json table with orWhere statement
     *
     * @param string $field
     * @param ...$params Must provide comparison sign and value (sign is optional)
     * @return self
     */
    public function orWhere(string $field, ...$params):self
    {
        $args = static::getQueryArguments($params);
        $query = $this->class::where($field, $args['sign'], $args['value'])->getQueried();
        $this->queried = array_unique(array_merge($this->queried, $query));
        return $this;
    }

    /**
     * Returns the first item of the collection. It will return null if nothing is found
     */
    public function first(){
        if(count($this->queried) == 0){
            return null;
        }
        $first_item = $this->queried[0];
        $primary_key = $this->class::getPrimaryKey();
        $query = (new Engine($this->class))->filter(function($item) use($primary_key, $first_item){
            return $item->$primary_key == $first_item;
        });
        return count($query) == 0 ? null : $query->first();
    }

    

    /**
     * Returns all data inside the json table
     */
    public function all()
    {
        return (new Engine($this->class))->filter(function($item){return true;});
    }

    /**
     * Gets the queried collection
     *
     * @return Collection
     */
    public function get():Collection
    {
        $queried = $this->queried;
        $primary_key = $this->class::getPrimaryKey();
        $collection = (new Engine($this->class))->filter(function($el) use($primary_key, &$queried){
            foreach($queried as $item){
                if($el->$primary_key == $item){
                    unset($item);
                    $queried = array_values($queried);
                    return true;
                }
            }
            return false;
        });
        return new Collection($collection);
    }

    /**
     * Gets the last primary key (as defined in the JsonModel) of the json file
     *
     * @return integer
     */
    public function getLastPrimaryKeyValue():int
    {
        return (new Engine($this->class))->currentPrimaryKey();
    }

    /**
     * Fills the table with data
     *
     * @param \guifcoelho\JsonModels\Model|\Illuminate\Support\Collection $data
     * @return void
     */
    public function fill($data):void{
        if(is_object($data)){
            if(!is_subclass_of($data, Model::class) && get_class($data) != Collection::class){
                throw new JsonModelsException("The data must be a subclass of '".Model::class."' or an instance of '".Collection::class."'");
            }
        }else{
            throw new JsonModelsException("The data must be a subclass of '".Model::class."' or an instance of '".Collection::class."'");
        }
        $primary_key = $this->class::getPrimaryKey();
        if(is_subclass_of($data, Model::class)){
            if($data->$primary_key == ""){
                throw new JsonModelsException("Primary key value not defined");
            }
        }else{
            foreach($data as $item){
                if($item->$primary_key == ""){
                    throw new JsonModelsException("Primary key value not defined");
                }
            }
        }

        if(is_subclass_of($data, Model::class)){
            $data = [$data->toJson()];
        }else{
            $data = array_map(function($el) use($primary_key){
                return json_encode($el);
            }, $data->toArray());
        }
        
        (new Engine($this->class))->insert($data);
    }
}
