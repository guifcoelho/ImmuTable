<?php

namespace guifcoelho\ImmuTable\Relations;

class HasOne {

    /**
     * The child model class
     *
     * @var string
     */
    protected $children_class = "";

    /**
     * The parent model instance
     */
    protected $parent;

    /**
     * The parent field name inside the children models
     *
     * @var string
     */
    protected $field_in_children_models = "";

    /**
     * The parent field value
     *
     * @var string|int
     */
    protected $field_in_parent;

    /**
     * Instanciates a new HasOne relation
     *
     * @param string $children_class
     * @param $parent
     * @param string $field_in_children_models
     * @param string $field_in_parent
     */
    public function __construct(string $children_class, $parent, string $field_in_children_models, string $field_in_parent){
        $this->children_class = $children_class;
        $this->parent = $parent;
        $this->field_in_children_models = $field_in_children_models == '' ? $parent->getForeignKey() : $field_in_children_models;
        $this->field_in_parent = $field_in_parent == '' ? $parent->getKeyName() : $field_in_parent;
    }

    /**
     * Returns the child of the parent model
     */
    public function first(){
        return $this->children_class::where($this->field_in_children_models, $this->parent->{$this->field_in_parent})->first();
    }
}