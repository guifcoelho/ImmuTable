<?php

namespace guifcoelho\ImmuTable\Relations;

use guifcoelho\ImmuTable\Relations\PivotQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;

class Pivot extends Model {

    protected $builder;

    public function builder(){
        if($this->builder == null){
            $query_builder = new QueryBuilder($this->getConnection());
            $this->builder = (new PivotQueryBuilder($query_builder))->setModel($this);
        }
        return $this->builder;
    }

    public static function defineTable($parent, $child){
        $classes = [
            Str::plural(Str::snake($parent)),
            Str::plural(Str::snake($child))
        ];
        sort($classes);
        return implode("_", $classes);
    }
}