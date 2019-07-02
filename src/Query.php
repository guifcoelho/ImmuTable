<?php

namespace guifcoelho\ImmuTable;

use JsonMachine\JsonMachine;
use guifcoelho\ImmuTable\Model;
use guifcoelho\ImmuTable\Engine;
use Illuminate\Support\Collection;
use guifcoelho\ImmuTable\Exceptions\ImmuTableException;

class Query{

    /**
     * Model class name. Must be a subclass of `\guifcoelho\ImmuTable\Model`
     *
     * @var string
     */
    protected $class = '';

    /**
     * List of queries to be executed
     *
     * @var array
     */
    protected $query = [];

    /**
     * Instanciates a Query object
     *
     * @param array $data
     */
    public function __construct(string $class){
        if(!is_subclass_of($class, Model::class)){
            throw new ImmuTableException("'{$class}' must be a subclass of '".Model::class."'");
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
            default: throw new ImmuTableException("The second argument must be a valid comparison sign");
        }
    }

    /**
     * Support function. Gets the query arguments
     *
     * @param array $args
     * @return array
     */
    protected function getQueryArguments(array $args):array
    {
        $sign = "==";
        if(count($args) == 1){
            $value = $args[0];
        }
        if(count($args) == 2){
            if(!is_string($args[0]) || strlen($args[0]) > 3){
                throw new ImmuTableException("The second argument must be a valid comparison sign");
            }
            $sign = $args[0];
            if(!is_numeric($args[1]) && !is_string($args[1])){
                throw new ImmuTableException("The third argument must be either a number or a string");
            }
            $value = $args[1];
        }
        return [
            'sign' => $sign,
            'value' => $value
        ];
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
        $args = $this->getQueryArguments($params);
        $this->query[] = array_merge(['operation' => 'where', 'field' => $field], $args);
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
        $this->query[] = array_merge(['operation' => 'orWhere', 'field' => $field], $this->getQueryArguments($params));
        return $this;
    }


    protected function run():Collection
    {
        if(count($this->query) == 0){
            return new Collection([]);
        }
        
        $where = array_filter($this->query, function($el){
            return $el['operation'] == 'where';
        });
        $orWhere = array_filter($this->query, function($el){
            return $el['operation'] == 'orWhere';
        });
        $self = $this;
        return (new Engine($this->class))->filter(function($item) use($self, $where, $orWhere){
            foreach($orWhere as $query){
                $field = $query['field'];
                if(!property_exists($item, $field)){
                    return false;
                }
                if($self->evalModelItem($item->$field, $query['sign'], $query['value'])){
                    return true;
                }
            }
            foreach($where as $query){
                $field = $query['field'];
                if(!property_exists($item, $field)){
                    return false;
                }
                if(!$self->evalModelItem($item->$field, $query['sign'], $query['value'])){
                    return false;
                }
            }
            return true;
        });
    }

    /**
     * Returns the first item of the collection. It will return null if nothing is found
     */
    public function first(){
        $query = $this->run();
        if(count($query) == 0){
            return null;
        }
        return $query->first();
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
        return $this->run();
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
     * @param \guifcoelho\ImmuTable\Model|\Illuminate\Support\Collection $data
     * @return void
     */
    public function fill($data):void{
        if(is_object($data)){
            if(!is_subclass_of($data, Model::class) && get_class($data) != Collection::class){
                throw new ImmuTableException("The data must be a subclass of '".Model::class."' or an instance of '".Collection::class."'");
            }
        }else{
            throw new ImmuTableException("The data must be a subclass of '".Model::class."' or an instance of '".Collection::class."'");
        }
        $primary_key = $this->class::getPrimaryKey();
        if(is_subclass_of($data, Model::class)){
            if($data->$primary_key == ""){
                throw new ImmuTableException("Primary key value not defined");
            }
        }else{
            foreach($data as $item){
                if($item->$primary_key == ""){
                    throw new ImmuTableException("Primary key value not defined");
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
