<?php

namespace guifcoelho\JsonModels\Tests\Unit;

use guifcoelho\JsonModels\Tests\TestCase;

use guifcoelho\JsonModels\Config;

class TestingJsonModelsClassTest extends TestCase
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
