<?php

namespace guifcoelho\ImmuTable;

use guifcoelho\ImmuTable\Config;
use guifcoelho\ImmuTable\Query;
use guifcoelho\ImmuTable\Exceptions\ImmuTableException;
use Illuminate\Contracts\Support\Arrayable;
use guifcoelho\ImmuTable\Relations\HasMany;

class Model implements Arrayable{

    use ModelRelations;

    /**
     * Table name
     *
     * @var string
     */
    protected $table = '';

    /**
     * Name of fields which will not be returned in toArray() (and similar) functions
     *
     * @var array
     */
    protected $hidden = [];


    /**
     * Name of the table's primary key. Unique items will be defined by this. It must be an integer between 1 and infinity.
     *
     * @var string
     */
    protected $primary_key = 'id';

    /**
     * All required table fields. If any field name is provided, the data loading will require them.
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
        return (new Config)->get('path_to_tables')."/{$table}.ndjson";
    }

    /**
     * Returns the list of table fields
     *
     * @return array
     */
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
   
    /**
     * Gets the primary key and sets it as foreign key style
     *
     * @param string $class
     * @return string
     */
    public static function getPrimaryFieldAsForeign(): string
    {
        $class_name = explode("\\", get_class(new static));
        $class_name = $class_name[count($class_name)-1];
        $class_name = strtolower($class_name);
        return $class_name . "_" . static::getPrimaryKey();
    }

    /**
     * Loads the data fields into the JsonModel. It will respect the listed fields.
     *
     * @param array $data
     * @return void
     */
    protected function loadData(array $data):void
    {
        if(count($this->fields) == 0){
            foreach($data as $field => $value){
                $this->$field = $value;
                $this->fields[] = $field;
            }
        }else{
            foreach($this->fields as $field){
                if(!array_key_exists($field, $data)){
                    throw new ImmuTableException("Field '{$field}' was not found in the data provided");
                }
                $this->$field = $data[$field];
            }
        }
        
    }

    /**
     * Querys the json table with
     *
     * @param string $field
     * @param ...$params Must provide comparison sign and value (sign is optional)
     * 
     * @return Query
     */
    public static function where(string $field, ...$params):Query
    {
        return (new Query(get_class(new static)))->where($field, ...array_values($params));
    }

    /**
     * Queries for a primary key
     *
     * @param int $value
     * @return void
     */
    public static function find(int $value){
        return (new Query(get_class(new static)))->where(static::getPrimaryKey(), $value)->first();
    }

    /**
     * Returns all data inside JsonModel table
     */
    public static function all(){
        return (new Query(get_class(new static)))->all();
    }

    
    /**
     * Extracts the data into an array.
     *
     * @return array
     */
    public function toArray():array
    {
        $arr = [];
        foreach($this->fields as $field){
            if(array_search($field, $this->hidden) === false){
                $arr[$field] = $this->$field;
            }
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
