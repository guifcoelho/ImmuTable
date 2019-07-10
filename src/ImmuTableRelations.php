<?php

namespace guifcoelho\ImmuTable;

use guifcoelho\ImmuTable\Relations\HasOne;
use guifcoelho\ImmuTable\Relations\HasMany;
use guifcoelho\ImmuTable\Relations\BelongsTo;
use guifcoelho\ImmuTable\Relations\BelongsToMany;

trait ImmuTableRelations{
    
    /**
     * Sets the `hasOne` to either Eloquent or Immutable model.
     *
     * @param string $child_class
     * @param string $field_in_owned_models
     * @param string $field
     * @return void
     */
    public function ImmuTable_hasOne(string $child_class, string $field_in_owned_model = '', string $field = ''){
        return new HasOne($child_class, $this, $field_in_owned_model, $field);
    }

    /**
     * Sets the `hasMany` to either Eloquent or Immutable model.
     *
     * @param string $child_class
     * @param string $field_in_owned_models
     * @param string $field
     * @return void
     */
    public function ImmuTable_hasMany(string $children_class, string $field_in_owned_models = '', string $field = ''){
        return new HasMany($children_class, $this, $field_in_owned_models, $field);
    }

    /**
     * Sets the `belongsTo` to either Eloquent or Immutable model.
     *
     * @param string $owner_class
     * @param string $field_in_owner_class
     * @param string $field
     */
    protected function ImmuTable_belongsTo(string $owner_class, string $field = '', string $field_in_owner_class = ''){
        return new BelongsTo($owner_class, $this, $field, $field_in_owner_class);
    }

    protected function ImmuTable_belongsToMany(string $parent_class, string $pivot_table = '', string $field_in_pivot = '', string $parent_field_in_pivot = ''){
        return new BelongsToMany($parent_class, $this, $pivot_table, $field_in_pivot, $parent_field_in_pivot);
    }
}
