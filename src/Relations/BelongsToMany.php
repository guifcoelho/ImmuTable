<?php

namespace guifcoelho\ImmuTable\Relations;

use guifcoelho\ImmuTable\Relations\Pivot;

class BelongsToMany{

    /**
     * The pivot table
     *
     * @var \guifcoelho\ImmuTable\Relations\Pivot
     */
    protected $pivot;

    /**
     * The parent class name
     *
     * @var string
     */
    protected $parent_class = '';

    /**
     * The parent field
     *
     * @var string
     */
    protected $parent_field = '';

    /**
     * The parent field name in the pivot table
     *
     * @var string
     */
    protected $parent_field_in_pivot = '';

    /**
     * The child model
     *
     * @var
     */
    protected $child;

    /**
     * The child model field
     *
     * @var string
     */
    protected $field = '';

    /**
     * The child model field name in the pivot table
     *
     * @var string
     */
    protected $field_in_pivot = '';

    /**
     * Instanciates a new BelongsToMay relation
     *
     * @param string $parent_class
     * @param $child
     * @param string $pivot_table
     * @param string $field_in_pivot
     * @param string $parent_field_in_pivot
     */
    public function __construct(
        string $parent_class = '',
        $child,
        string $pivot_table = '',
        string $field_in_pivot = '',
        string $parent_field_in_pivot = '',
        string $field = '',
        string $parent_field = ''
    ){
        if($pivot_table == ''){
            $pivot_table = Pivot::defineTable(class_basename(new $parent_class()), class_basename($child));
        }
        $this->setPivot($pivot_table);
        $this->parent_class = $parent_class;
        $this->child = $child;
        $this->field = $field == '' ? $child->getKeyName() : $field;
        $this->field_in_pivot = $field_in_pivot == '' ? $child->getForeignKey() : $field_in_pivot;
        $this->parent_field = $parent_field == '' ? (new $parent_class)->getKeyName() : $parent_field;
        $this->parent_field_in_pivot = $parent_field_in_pivot == '' ? (new $parent_class)->getForeignKey() : $parent_field_in_pivot;
    }

    /**
     * Sets the pivot table for the relation
     *
     * @param string $pivot_table
     * @return void
     */
    protected function setPivot(string $pivot_table):void
    {
        $fillable = [$this->field_in_pivot, $this->parent_field_in_pivot];
        $this->pivot = (new Pivot())->setTable($pivot_table)->fillable($fillable);
    }

    /**
     * Adds a new parent to the pivot
     *
     * @param string|int $id
     * @return self
     */
    public function attach($id):self
    {
        $query = $this->pivot->builder()->where($this->field_in_pivot, $this->child->{$this->field})
            ->where($this->parent_field_in_pivot, $id)
            ->first();

        if(!$query){
            $this->pivot->builder()->createFromThis([
                $this->field_in_pivot => $this->child->{$this->field},
                $this->parent_field_in_pivot => $id
            ]);
        }
        return $this;
    }

    /**
     * Detaches the parent from the pivot
     *
     * @param string|int|array $id
     * @return self
     */
    public function detach(...$id):self{
        if(func_num_args() == 0){
            $this->pivot
                ->builder()
                ->where($this->field_in_pivot, $this->child->{$this->field})
                ->get()
                ->each(function($item){
                    $item->delete();
                });
        }else{
            if(count($id)==1){
                $id = $id[0];
            }
            if(is_array($id)){
                $this->pivot
                    ->builder()
                    ->where($this->field_in_pivot, $this->child->{$this->field})
                    ->whereIn($this->parent_field_in_pivot, $id)
                    ->get()
                    ->each(function($item){
                        $item->delete();        
                    });
            }else{
                $this->pivot
                    ->builder()
                    ->where($this->field_in_pivot, $this->child->{$this->field})
                    ->where($this->parent_field_in_pivot, '=', $id)
                    ->get()
                    ->each(function($item){
                        $item->delete();    
                    });
            }
        }
        return $this;
    }

    /**
     * Syncs the pivot table with the data
     * 
     * @var string|int|array $id
     * @return self
     */
    public function sync(...$id):self{
        $this->detach();
        if(func_num_args() > 0){
            if(count($id) == 1){
                $id = $id[0];
            }
            if(is_array($id)){
                foreach($id as $item){
                    $this->attach($item);
                }
            }else{
                $this->attach($id);
            }
        }
        return $this;
    }

    public function save($model):self{
        $this->attach($model->{$this->parent_field});
        return $this;
    }

    public function getPivot():Pivot
    {
        return $this->pivot;
    }
}