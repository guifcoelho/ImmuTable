<?php

namespace guifcoelho\ImmuTable\Relations;

use Illuminate\Support\Collection;
use guifcoelho\ImmuTable\Exceptions\ImmuTableException;
use guifcoelho\ImmuTable\Model;

class HasMany extends HasOne{

    /**
     * Returns a collection of children models
     *
     * @return Collection
     */
    public function get():Collection
    {
        return $this->children::where($this->field_in_owned_models, $this->field_value_in_owner)->get();
    }
}