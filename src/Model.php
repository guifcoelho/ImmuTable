<?php

namespace guifcoelho\ImmuTable;

use guifcoelho\ImmuTable\Config;
use guifcoelho\ImmuTable\Query;
use guifcoelho\ImmuTable\Exceptions\ImmuTableException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use guifcoelho\ImmuTable\Relations\ImmuTableRelations;

class Model implements Arrayable{

    use ImmuTableRelations;
    
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
     * All required table fields. When loading data, we will only read the fields in the array.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * The array of booted models.
     *
     * @var array
     */
    protected static $booted = [];

    /**
     * The array of trait initializers that will be called on each new instance.
     *
     * @var array
     */
    protected static $traitInitializers = [];

    /**
     * Instanciates a new ImmuTable model
     *
     * @param array $dados
     */
    public function __construct(array $data = []){
        
        $this->bootIfNotBooted();

        $this->initializeTraits();
        
        if(count($data) > 0){
            $this->loadData($data);
        }
    }

    /**
     * Loads the configuration file and returns the table path
     *
     * @return string
     */
    public function getTablePath(): string
    {
        return (new Config)->get('path_to_tables')."/{$this->table}.ndjson";
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
     * Returns the table primary key name
     *
     * @return string
     */
    public function getKeyName():string
    {
        return $this->primary_key;
    }


    /**
     * Returns the primary key name as a foreign key
     * 
     * @return string
     */
    public function getForeignKey(): string
    {
        return Str::snake(class_basename($this)) . "_" . $this->getKeyName();
    }

    /**
     * Loads data into the model.
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
     * Check if the model needs to be booted and if so, do it.
     *
     * @return void
     */
    protected function bootIfNotBooted()
    {
        if (! isset(static::$booted[static::class])) {
            static::$booted[static::class] = true;

            static::boot();
        }
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        static::bootTraits();
    }

    /**
     * Boot all of the bootable traits on the model.
     *
     * @return void
     */
    protected static function bootTraits()
    {
        $class = static::class;

        $booted = [];

        static::$traitInitializers[$class] = [];

        foreach (class_uses_recursive($class) as $trait) {
            $method = 'boot'.class_basename($trait);

            if (method_exists($class, $method) && ! in_array($method, $booted)) {
                forward_static_call([$class, $method]);

                $booted[] = $method;
            }

            if (method_exists($class, $method = 'initialize'.class_basename($trait))) {
                static::$traitInitializers[$class][] = $method;

                static::$traitInitializers[$class] = array_unique(
                    static::$traitInitializers[$class]
                );
            }
        }
    }

    /**
     * Initialize any initializable traits on the model.
     *
     * @return void
     */
    protected function initializeTraits()
    {
        foreach (static::$traitInitializers[static::class] as $method) {
            $this->{$method}();
        }
    }

    /**
     * Clear the list of booted models so they will be re-booted.
     *
     * @return void
     */
    public static function clearBootedModels()
    {
        static::$booted = [];

        static::$globalScopes = [];
    }

    /**
     * Queries the model's table
     *
     * @param string $field
     * @param ...$params Must provide comparison sign and/or value (sign is optional)
     * 
     * @return Query
     */
    public static function where(string $field, ...$params):Query
    {
        return (new Query(get_class(new static)))->where($field, ...array_values($params));
    }

    /**
     * Queries for a primary key value
     *
     * @param int $id
     * @return void
     */
    public static function find(int $id){
        return (new Query(get_class(new static)))->where(static::getPrimaryKey(), $id)->first();
    }

    /**
     * Returns all data inside model's table
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
