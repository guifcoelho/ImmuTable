<?php

namespace guifcoelho\ImmuTable;

use guifcoelho\ImmuTable\Relations\HasMany;
use guifcoelho\ImmuTable\Relations\BelongsTo;

trait ModelRelations {

    /**
     * ```hasMany``` relationship
     *
     * @param  string $child_class
     * @param string $field_in_owned_models
     * @param string $field
     */
    protected function hasMany(string $child_class, string $field_in_owned_models = '', string $field = ''){
        if($field_in_owned_models == ''){
            $field_in_owned_models = static::getPrimaryFieldAsForeign();
        } 
        if($field == ''){
            $field = $this->primary_key;
        }
        return new HasMany($child_class, $field_in_owned_models, $this->$field);
    }

    /**
     * ```belongsTo``` relationship
     *
     * @param string $owner_class
     * @param string $field
     * @param string $field_in_owner_class
     */
    protected function belongsTo(string $owner_class, string $field = '', string $field_in_owner_class = ''){
        if($field == ''){
            $field = $owner_class::getPrimaryFieldAsForeign();
        }
        return new BelongsTo($owner_class, $field_in_owner_class, $this->$field);
    }

}