<?php

namespace guifcoelho\ImmuTable;

use guifcoelho\ImmuTable\Relations\HasOne;
use guifcoelho\ImmuTable\Relations\HasMany;
use guifcoelho\ImmuTable\Relations\BelongsTo;
use guifcoelho\ImmuTable\Relations\BelongsToMany;

trait ImmuTableRelations{
    
    /**
     * Sets the `hasOne` relation to either Eloquent or Immutable models.
     *
     * @param string $child_class
     * @param string $field_in_child_models
     * @param string $field
     * @return void
     */
    public function ImmuTable_hasOne(string $child_class, string $field_in_child_model = '', string $field = ''){
        return new HasOne($child_class, $this, $field_in_child_model, $field);
    }

    /**
     * Sets the `hasMany` relation to either Eloquent or Immutable models.
     *
     * @param string $child_class
     * @param string $field_in_child_models
     * @param string $field
     * @return void
     */
    public function ImmuTable_hasMany(string $children_class, string $field_in_child_models = '', string $field = ''){
        return new HasMany($children_class, $this, $field_in_child_models, $field);
    }

    /**
     * Sets the `belongsTo` relation to either Eloquent or Immutable models.
     *
     * @param string $parent_class
     * @param string $field_in_parent_class
     * @param string $field
     */
    protected function ImmuTable_belongsTo(string $parent_class, string $field = '', string $field_in_parent_class = ''){
        return new BelongsTo($parent_class, $this, $field, $field_in_parent_class);
    }

    /**
     * Sets the `belongsToMany` relation to either Eloquent or ImmuTable models
     *
     * @param string $parent_class
     * @param string $pivot_table
     * @param string $field_in_pivot
     * @param string $parent_field_in_pivot
     * @return void
     */
    protected function ImmuTable_belongsToMany(
        string $parent_class, 
        string $pivot_table = '', 
        string $field_in_pivot = '', 
        string $parent_field_in_pivot = '',
        string $field = '', 
        string $field_in_parent = ''
    ){
        return new BelongsToMany($parent_class, $this, $pivot_table, $field_in_pivot, $parent_field_in_pivot, $field, $field_in_parent);
    }
}
