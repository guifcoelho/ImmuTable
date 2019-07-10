<?php

namespace guifcoelho\ImmuTable\Relations;

class BelongsTo {

    /**
     * The owner model class
     *
     * @var string
     */
    protected $parent_class = "";

    /**
     * The child model instance
     */
    protected $child;

    /**
     * The field name in the owner model
     *
     * @var string
     */
    protected $field_in_parent = "";

    /**
     * The owner field inside the child model
     *
     * @var string|int
     */
    protected $field_in_child = '';

    public function __construct(string $parent_class, $child, string $field_in_child, string $field_in_parent){
        $this->parent_class = $parent_class;
        $this->child = $child;

        $parent = new $parent_class();
        $this->field_in_parent = $field_in_parent == '' ? $parent->getKeyName() : $field_in_parent;
        $this->field_in_child = $field_in_child == '' ? $parent->getForeignKey() : $field_in_child;
    }

    /**
     * Returns the owner model
     */
    public function first(){
        return $this->parent_class::where($this->field_in_parent, $this->child->{$this->field_in_child})->first();
    }
}