<?php

namespace guifcoelho\ImmuTable\Relations;

use Illuminate\Database\Eloquent\Builder;
use guifcoelho\ImmuTable\Relations\EloquentPivot;

class EloquentPivotQueryBuilder extends Builder {
    
    /**
     * Get all of the models from the database.
     *
     * @param  array|mixed  $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all($columns = ['*'])
    {
        return $this->model->newQuery()->get(
            is_array($columns) ? $columns : func_get_args()
        );
    }

    /**
     * Save a new model and return the instance.
     *
     * @param  array  $attributes
     * @return \guifcoelho\ImmuTable\Relations\EloquentPivot
     */
    public function createFromThis(array $attributes = []):EloquentPivot
    {
        return tap($this->newPivotItem($attributes), function ($instance) {
            $instance->save();
        });
    }

    /**
     * Returns a new instance of `EloquentPivot` with the current properties. Attaches a list of attributes.
     *
     * @param array $attributes
     * @return EloquentPivot
     */
    protected function newPivotItem(array $attributes = []):EloquentPivot
    {
        $pivot = (new EloquentPivot)->setTable($this->model->getTable())->fillable($this->model->getFillable());
        foreach ($attributes as $key=>$value){
            $pivot->setAttribute($key, $value);
        }
        return $pivot;
    }
}