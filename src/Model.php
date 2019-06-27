<?php

namespace guifcoelho\JsonModels;

use guifcoelho\JsonModels\Config;
use guifcoelho\JsonModels\Query;

class Model{

    /**
     * Table name
     *
     * @var string
     */
    protected $table = '';

    /**
     * Name of field which will not be returned in toArray() (and similar) function
     *
     * @var array
     */
    protected $hidden = [];


    /**
     * Name of the table's primary key. Unique items will be defined by this.
     *
     * @var string
     */
    protected $primary_key = 'id';

    /**
     * All table fields. Will be replaced after data loading
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Creates a new JsonModel object
     *
     * @param array $dados
     */
    public function __construct(array $data = []){
        if(count($data) > 0){
            $this->loadData($data);
        }
    }

    /**
     * Loads the configuration file and returns the table path
     *
     * @return string
     */
    public static function getTablePath(): string
    {
        $table = (new static)->table;
        return (new Config)->get('path_to_tables')."/{$table}.json";
    }

    /**
     * Returns the table primary key
     *
     * @return integer
     */
    public static function getPrimaryKey():string
    {
        return (new static)->primary_key;
    }

    /**
     * Loads the data fields into the JsonModel
     *
     * @param array $data
     * @return void
     */
    protected function loadData(array $data):void
    {
        foreach($data as $field => $value){
            $this->$field = $value;
            $this->fields[] = $field;
        }
    }

    /**
     * Static function. Querys the json table
     *
     * @param string $field
     * @param string $sign
     * @param mixed $value
     */
    public static function where(string $field, ...$params)
    {
        $args = func_get_args();
        $sign = "==";
        if(count($args) == 2){
            $value = $args[1];
        }
        if(count($args) == 3){
            if(!is_string($args[1]) || strlen($args[1]) > 3){
                throw new \Exception("The second argument must be a comparison sign");
            }
            $sign = $args[1];
            if(!is_numeric($args[2]) && !is_string($args[2])){
                throw new \Exception("The third argument must be either a number or a string");
            }
            $value = $args[2];
        }
        return (new static)->query($field, $sign, $value);
    }

    /**
     * Querys the json table
     *
     * @param string $field
     * @param string $sign
     * @param mixed $value
     */
    protected function query(string $field, string $sign = '==', $value)
    {
        return (new Query(get_class($this)))->where($field, $sign, $value);
    }


    /**
     * Função static. Retorns a collection of JsonModels
     *
     * @return Query
     */
    public static function all(){
        return (new static)->queryAll();
    }

    /**
     * Retorns a collection of JsonModels
     *
     * @return Query
     */
    protected function queryAll(){
        return (new Query(get_class($this)))->all();
    }

    /**
     * ```belongsToOne``` relationship
     *
     * @param $owner_model
     * @param string $field_of_this
     * @param string $field_of_owner
     * @return void
     */
    protected function belongsToOneJsonModel($owner_model, string $field_of_this, string $field_of_owner){
        $query = (new $owner_model)->where($field_of_owner, '==', $this->$field_of_this)->first();
        return $query != null ? $query : null;
    }

    /**
     * ```hasMany``` relationship
     *
     * @param  $children_model
     * @param string $field_of_this
     * @param string $field_of_children
     * @return boolean
     */
    protected function hasManyJsonModel($children_model, string $field_of_this, string $field_of_children){
        $query = (new $children_model)->where($field_of_children, '==', $this->$field_of_this);
        return $query != null ? $query : null;
    }

    /**
     * Extracts the data into an array
     *
     * @return array
     */
    public function toArray():array
    {
        $arr = [];
        foreach($this->fields as $field){
            $arr[$field] = $this->$field;
        }
        return $arr;
    }

    /**
     * Extracts the data into json
     *
     * @return string
     */
    public function toJson():string
    {
        return json_encode($this->toArray());
    }
}
