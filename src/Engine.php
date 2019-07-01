<?php

namespace guifcoelho\JsonModels;

use guifcoelho\JsonModels\Model;
use Illuminate\Support\Collection;
use guifcoelho\JsonModels\Config;
use guifcoelho\JsonModels\Exceptions\JsonModelsException;

class Engine{

    /**
     * Model class name. Must be a subclass of `\guifcoelho\JsonModels\Model`
     *
     * @var string
     */
    protected $class = "";

    /**
     * Size of each chunk of loaded data
     *
     * @var integer
     */
    protected $chunk_size = 0;

    /**
     * Stream resource to load data
     *
     * @var stream
     */
    protected $handle;

    /**
     * Current position within the data file
     *
     * @var integer
     */
    protected $pos = 0;

    /**
     * Current loaded data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Instanciates a new Client class
     *
     * @param string $class
     */
    public function __construct(string $class){
        if(!is_subclass_of($class, Model::class)){
            throw new JsonModelsException("'{$class}' is not a subclass of '".Model::class."'");
        }
        $this->class = $class;
        $this->chunk_size = (new Config)->get('chunk_size');
    }

    /**
     * Loads a new chunk of data. It will return false after the end of the file
     *
     * @return array|boolean
     */
    protected function chunk()
    {
        $file = $this->class::getTablePath();
        if(!file_exists($file)){
            //Creates da table file if it does not exists
            file_put_contents($file, "");
        }
        try{
            if($this->handle == null || \get_resource_type($this->handle) != 'stream'){
                $this->handle = fopen($file, "r");
            }
            if(!feof($this->handle)){
                $chunk = fread($this->handle, $this->chunk_size);
                $this->pos += strlen($chunk);
                $arr_chunk = explode("\n", $chunk);
                
                $last_chunk = $arr_chunk[count($arr_chunk)-1];
                if(\substr($last_chunk, strlen($last_chunk)-1) != "}"){
                    //It did not read the whole line
                    //Rollback pointer to last line
                    $this->pos -= strlen($last_chunk);
                    fseek ($this->handle , $this->pos);
                    array_pop($arr_chunk);
                    $search_null = array_search(null, $arr_chunk);
                    if($search_null !== false){
                        unset($arr_chunk[$search_null]);
                    };
                }
                foreach($arr_chunk as &$item){
                    $item = new $this->class(json_decode($item, true));
                }
                return $arr_chunk;
            }else{
                fclose($this->handle);
                return false;
            }
        }
        catch(Exception $e)
        {
            throw new JsonModelsException("Engine::" . $e->getMessage());
            return false;
        }
    }

    /**
     * Loads and transforms item by item
     *
     * @param callable $callback
     * @return null|\guifcoelho\JsonModels\Model|\Illuminate\Support\Collection
     */
    public function map(callable $callback)
    {
        $data = [];
        while($chunk = $this->chunk()){
            foreach($chunk as $item){
                $data[] = $callback($item);
            }
        }
        if(count($data) == 0){
            return null;
        }elseif(count($data) == 1){
            return $data[0];
        }else{
            return new Collection($data);
        }
    }

    /**
     * Loads and filters data
     *
     * @param callable $callback
     * @return null|\guifcoelho\JsonModels\Model|\Illuminate\Support\Collection
     */
    public function filter(callable $callback){

        $filter = [];
        while($chunk = $this->chunk()){
            foreach($chunk as $item){
                if($callback($item)){
                    $filter[] = $item;
                }
            }
        }
        
        if(count($filter) == 0){
            return null;
        }elseif(count($filter) == 1){
            return $filter[0];
        }else{
            return new Collection($filter);
        }

    }

    /**
     * Gets the last primary key (as defined in the JsonModel) of the json file
     *
     * @return integer
     */
    public function currentPrimaryKey():int
    {
        $primary_key = $this->class::getPrimaryKey();
        $last_primary_key_value = 0;
        while($chunk = $this->chunk()){
            foreach($chunk as $item){
                $last_primary_key_value = max($last_primary_key_value, $item->$primary_key);    
            }
        }
        return $last_primary_key_value;
    }

    /**
     * Inserts data at the end of the table
     *
     * @param array $data
     * @return void
     */
    public function insert(array $data):void
    {
        file_put_contents($this->class::getTablePath(), implode("\n", $data)."\n", FILE_APPEND);
    }
}