<?php

namespace guifcoelho\ImmuTable\Relations;

use Illuminate\Support\Collection;
use guifcoelho\ImmuTable\Exceptions\ImmuTableException;
use guifcoelho\ImmuTable\Model;

class BelongsTo {

    /**
     * The owner model class
     *
     * @var string
     */
    protected $owner = "";

    /**
     * The field name in the owner model
     *
     * @var string
     */
    protected $field_in_owner = "";

    /**
     * The owner field value inside the child model
     *
     * @var string|int
     */
    protected $field_value_in_owned;

    public function __construct(string $owner, string $field_in_owner, $field_value_in_owned){
        $this->owner = $owner;
        $this->field_in_owner = $field_in_owner == '' ? $owner::getPrimaryKey() : $field_in_owner;
        $this->field_value_in_owned = $field_value_in_owned;
    }

    /**
     * Returns the owner model
     */
    public function first(){
        return $this->owner::where($this->field_in_owner, $this->field_value_in_owned)->first();
    }
}