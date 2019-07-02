<?php

namespace guifcoelho\ImmuTable\Tests\Unit;

use guifcoelho\ImmuTable\Tests\TestCase;

use guifcoelho\ImmuTable\Config;

class TestingImmuTableClassTest extends TestCase
{

    protected function deleteDir($path) {
        if (empty($path)) { 
            return false;
        }
        return is_file($path) ?
                @unlink($path) :
                array_map([$this, __FUNCTION__], glob($path.'/*')) == @rmdir($path);
    }


    public function test_setting_tables_path()
    {
        $storage_path = __DIR__.'/../../src/storage';
        $this->deleteDir($storage_path);
        $this->setTablesPath();
        $this->assertTrue(is_dir((new Config)->get('path_to_tables')));
    }
}
