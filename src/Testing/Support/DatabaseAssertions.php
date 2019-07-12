<?php

namespace guifcoelho\ImmuTable\Testing\Support;

use PHPUnit\Framework\Assert as PHPUnit;

trait DatabaseAssertions{

    protected function assertDatabaseHas(string $table, $data)
    {
        $this->assertTrue($this->manager->getConnection()->table($table)->where($data)->count() > 0);
    }

    protected function assertDatabaseHasNot(string $table, $data)
    {
        $this->assertTrue($this->manager->getConnection()->table($table)->where($data)->count() == 0);
    }

}