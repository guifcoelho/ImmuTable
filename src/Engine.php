<?php

namespace guifcoelho\ImmuTable;

use guifcoelho\ImmuTable\Model;
use Illuminate\Support\Collection;
use guifcoelho\ImmuTable\Config;
use guifcoelho\ImmuTable\Exceptions\ImmuTableException;

class Engine{

    /**
     * Model class name. Must be a subclass of `\guifcoelho\ImmuTable\Model`
     *
     * @var string
     */
    protected $class = "";

    /**
     * The size of each chunk of loaded data
     *
     * @var integer
     */
    protected $chunk_size = 0;

    /**
     * The stream resource
     *
     * @var stream
     */
    protected $handle;

    /**
     * Current position within the file
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
     * Instanciates a new Engine object
     *
     * @param string $class
     */
    public function __construct(string $class){
        if(!is_subclass_of($class, Model::class)){
            throw new ImmuTableException("'{$class}' is not a subclass of '".Model::class."'");
        }
        $this->class = $class;
        $this->chunk_size = (new Config)->get('chunk_size');
    }

    /**
     * Loads a new data chunk. It will return false after the end of the file.
     *
     * @return array|boolean
     */
    protected function loadNextChunk()
    {
        $file = (new $this->class)->getTablePath();
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
            throw new ImmuTableException("Engine::" . $e->getMessage());
            return false;
        }
    }

    /**
     * Filters data against a `$callback` function
     *
     * @param callable $callback
     * @return null|\guifcoelho\ImmuTable\Model|\Illuminate\Support\Collection
     */
    public function filter(callable $callback){

        $filter = [];
        while($chunk = $this->loadNextChunk()){
            foreach($chunk as $item){
                if($callback($item)){
                    $filter[] = $item;
                }
            }
        }
        return new Collection($filter);
    }

    /**
     * Gets the highest value for the model's primary key
     *
     * @return integer
     */
    public function currentPrimaryKey():int
    {
        $primary_key = (new $this->class)->getKeyName();
        $last_primary_key_value = 0;
        while($chunk = $this->loadNextChunk()){
            foreach($chunk as $item){
                $last_primary_key_value = max($last_primary_key_value, $item->$primary_key);    
            }
        }
        return $last_primary_key_value;
    }

    /**
     * Inserts data at the end of the table.
     * 
     * IT SHOULD ONLY BE USED IN TESTING OR PROTOTYPING
     *
     * @param array $data
     * @return void
     */
    public function insert(array $data):void
    {
        file_put_contents((new $this->class)->getTablePath(), implode("\n", $data)."\n", FILE_APPEND);
    }
}