<?php

namespace guifcoelho\ImmuTable\Relations;

use guifcoelho\ImmuTable\Relations\PivotQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;

class Pivot extends Model {

    /**
     * The query builder instance
     *
     * @var \guifcoelho\ImmuTable\Relations\PivotQueryBuilder
     */
    protected $builder;

    /**
     * Returns the query builder instance
     *
     * @return \guifcoelho\ImmuTable\Relations\PivotQueryBuilder
     */
    public function builder():PivotQueryBuilder
    {
        if($this->builder == null){
            $query_builder = new QueryBuilder($this->getConnection());
            $this->builder = (new PivotQueryBuilder($query_builder))->setModel($this);
        }
        return $this->builder;
    }

    /**
     * Returns the pivot table name according to parent and child model classes
     *
     * @param string $parent
     * @param string $child
     * @return string
     */
    public static function defineTable(string $parent, string $child):string
    {
        $classes = [
            Str::plural(Str::snake($parent)),
            Str::plural(Str::snake($child))
        ];
        sort($classes);
        return implode("_", $classes);
    }
}