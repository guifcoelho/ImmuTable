<?php

namespace guifcoelho\ImmuTable\Testing\Support;

use PHPUnit\Framework\Assert as PHPUnit;

trait DatabaseAssertions{

    protected function assertDatabaseHas(string $table, $data)
    {
        $this->assertTrue($this->capsule->getConnection()->table($table)->where($data)->count() > 0);
    }

}