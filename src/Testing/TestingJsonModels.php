<?php

namespace guifcoelho\JsonModels\Testing;

use guifcoelho\JsonModels\Testing\Support\ArrayAssertions;
use guifcoelho\JsonModels\Testing\Support\JsonTablesAssertions;
use guifcoelho\JsonModels\Factory;
use guifcoelho\JsonModels\Model;
use guifcoelho\JsonModels\Exceptions\Exception;
use guifcoelho\JsonModels\Config;

trait TestingJsonModels
{
    public function setUp()
    {
        parent::setUp();
        $this->bootstrap();
    }

    protected function arrays(){
        return new ArrayAssertions();
    }

    protected function jsontables(){
        return new JsonTablesAssertions();
    }

    public function bootstrap(){
        require __DIR__."/Support/Functions.php";
        $tables_path = (new Config)->get('path_to_tables');
        if(!file_exists($tables_path)){
            mkdir($tables_path, 0777, true);
        }
    }
}
