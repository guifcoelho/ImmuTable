<?php

namespace guifcoelho\ImmuTable\Relations;

use Illuminate\Support\Collection;

class HasMany extends HasOne{

    /**
     * Returns a collection of children models
     *
     * @return Collection
     */
    public function get():Collection
    {
        return $this->children_class::where($this->field_in_children_models, $this->parent->{$this->field_in_parent})->get();
    }
}