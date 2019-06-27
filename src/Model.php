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

    public function getFields():array
    {
        return $this->fields;
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

    public static function getName():string
    {
        $class = explode("\\", get_class(new static()));
        return strtolower($class[count($class)-1]);

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
     * @param $owner_class
     * @param string $field
     * @param string $field_in_owner_class
     */
    protected function belongsToOneJsonModel($owner_class, string $field = '', string $field_in_owner_class = ''){
        $owner_primary_key = $owner_class::getPrimaryKey();
        $owner_std_name = $owner_class::getName();
        $field = $field == '' ? "{$owner_std_name}_{$owner_primary_key}" : $field;
        $field_in_owner_class = $field_in_owner_class == '' ? $owner_primary_key : $field_in_owner_class;
        return $owner_class::where($field_in_owner_class, $this->$field)->first();
    }

    /**
     * ```hasMany``` relationship
     *
     * @param  $child_class
     * @param string $field_in_owned_class
     * @param string $field
     */
    protected function hasManyJsonModel($child_class, string $field_in_owned_class = '', string $field = ''){
        $owner_std_name = static::getName();
        $field_in_owned_class = $field_in_owned_class == '' ? "{$owner_std_name}_{$this->primary_key}" : $field_in_owned_class;
        $field = $field == '' ? $this->primary_key : $field;
        return $child_class::where($field_in_owned_class, $this->$field)->get();
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
