<?php

namespace guifcoelho\ImmuTable\Relations;

use Illuminate\Database\Eloquent\Builder;
use guifcoelho\ImmuTable\Relations\Pivot;

class PivotQueryBuilder extends Builder {
    
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
     * @return bool
     */
    public function createFromThis(array $attributes = []):bool
    {
        return $this->newPivotItem($attributes)->save();
    }

    /**
     * Returns a new instance of `Pivot` with the current properties. Attaches a list of attributes.
     *
     * @param array $attributes
     * @return Pivot
     */
    protected function newPivotItem(array $attributes = []):Pivot
    {
        $pivot = (new Pivot)->setTable($this->model->getTable())->fillable($this->model->getFillable());
        foreach ($attributes as $key=>$value){
            $pivot->setAttribute($key, $value);
        }
        return $pivot;
    }
}