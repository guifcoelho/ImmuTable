<?php

namespace guifcoelho\ImmuTable\Tests;

use PHPUnit\Framework\TestCase as PHPUnit;
use guifcoelho\ImmuTable\Tests\Support\TestingImmuTable;


/**
 * Class TestCase
 *
 * @author  Mahmoud Zalt  <mahmoud@zalt.me>
 */
class TestCase extends PHPUnit
{
    use TestingImmuTable;

    protected $factory_path = __DIR__."/Unit/SampleModels/Factory";

    public function __construct()
    {
        parent::__construct();
    }

    public function setUp():void{
        parent::setUp();
        $this->bootstrap();
        $this->refreshImmuTable();
    }

    public function tearDown():void
    {
        parent::tearDown();
    }    
}
