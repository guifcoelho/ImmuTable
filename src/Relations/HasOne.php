<?php

namespace guifcoelho\ImmuTable\Relations;

use Illuminate\Support\Collection;
use guifcoelho\ImmuTable\Exceptions\ImmuTableException;
use guifcoelho\ImmuTable\Model;

class HasOne {

    /**
     * The children model class
     *
     * @var string
     */
    protected $children = "";

    /**
     * The owner field name inside the children models
     *
     * @var string
     */
    protected $field_in_owned_models = "";

    /**
     * The owner field value
     *
     * @var string|int
     */
    protected $field_value_in_owner;

    public function __construct(string $children, string $field_in_owned_models, $field_value_in_owner){
        $this->children = $children;
        $this->field_in_owned_models = $field_in_owned_models;
        $this->field_value_in_owner = $field_value_in_owner;
    }

    /**
     * Returns the first child of the owner model
     */
    public function first(){
        return $this->children::where($this->field_in_owned_models, $this->field_value_in_owner)->first();
    }
}