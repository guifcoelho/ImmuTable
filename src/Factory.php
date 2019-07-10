<?php

namespace guifcoelho\ImmuTable;

use Symfony\Component\Finder\Finder;
use guifcoelho\ImmuTable\Model;
use guifcoelho\ImmuTable\Client;
use Illuminate\Support\Collection;
use guifcoelho\ImmuTable\Exceptions\ImmuTableException;

class Factory{

    /**
     * List of factory definitions of each JsonModel class
     *
     * @var array
     */
    protected $definitions = [];

    /**
     * Path to factories folder
     *
     * @var string
     */
    protected $path = "";

    /**
     * The Faker object
     *
     * @var \Faker\Factory
     */
    protected $faker;

    /**
     * Subclass of \guifcoelho\ImmuTable\Model
     *
     * @var string
     */
    protected $class = "";

    /**
     * The size of the collection to make or create
     *
     * @var integer
     */
    protected $size = 1;

    /**
     * Instanciates a new Factory object
     *
     * @param string $class
     * @param integer $size
     * @param string $path
     */
    public function __construct(string $class, int $size = 1, string $path = ""){
        $this->class = $class;
        $this->size = $size;
        $this->path = function_exists('database_path') ? database_path('factories') : $path;
        $this->faker = \Faker\Factory::create();
    }

    /**
     * Loads the factory for the given model class
     *
     * @return Factory
     */
    public function load():self{
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
     * It will not register if `$class` is not subclass of `\guifcoelho\ImmuTable\Model`
     *
     * @param string $class
     * @param callable $attributes
     * @return Factory
     */
    protected function define(string $class, callable $attributes):self{
        if(is_subclass_of($class, Model::class)){
            $this->definitions[$class] = $attributes;
        }
        return $this;
    }

    /**
     * Builds the definitions and attributes provided in the factory
     *
     * @param array $attributes
     */
    protected function buildData(array $attributes = []){
        if(!array_key_exists($this->class, $this->definitions)){
            throw new ImmuTableException("No definitions set for class '{$this->class}'");
        }
        $collection = [];
        $primary_key = (new $this->class)->getKeyName();
        foreach(range(1, $this->size) as $item){
            $definitions = call_user_func($this->definitions[$this->class], $this->faker);
            foreach($attributes as $key => $value){
                $definitions[$key] = $value;
            }
            $definitions[$primary_key] = '';
            $collection[] = new $this->class($definitions);
        }
        
        if($this->size == 1){
            return $collection[0];
        }
        
        
        return new Collection($collection);
    }

    /**
     * Makes a JsonModel object or a collection of JsonModel objects.
     *
     * @param array $attributes
     */
    public function make(array $attributes = [])
    {
        $data_built = $this->buildData($attributes);
        /*
        | Run afterMaking methods...
        */
        return $data_built;
    }

    /**
     * Creates a JsonModel object or a collection of JsonModel objects. It will write into the json file
     *
     * @param array $attributes
     */
    public function create(array $attributes = []){
        $data_built = $this->buildData($attributes);
        /*
        | Run afterCreating() methods...
        */
        $primary_key = (new $this->class)->getKeyName();
        $client = new Query($this->class);
        $last_primary_key = $client->getLastPrimaryKeyValue();

        if(is_subclass_of($data_built, Model::class)){
            $data_built->$primary_key = ++$last_primary_key;
        }else{
            foreach($data_built as $item){
                $item->$primary_key = ++$last_primary_key;
            }
        }

        $client->fill($data_built);
        return $data_built;
    }

}