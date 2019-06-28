<?php

namespace guifcoelho\JsonModels\Tests;

use PHPUnit\Framework\TestCase as PHPUnit;
use guifcoelho\JsonModels\Testing\TestingJsonModels;


/**
 * Class TestCase
 *
 * @author  Mahmoud Zalt  <mahmoud@zalt.me>
 */
class TestCase extends PHPUnit
{
    use TestingJsonModels;

    protected $factory_path = __DIR__."/Unit/SampleModels/Factory";

    public function __construct()
    {
        parent::__construct();
    }

    public function setUp():void{
        parent::setUp();
        $this->bootstrap();
        $this->refreshJsonModels();
    }

    public function tearDown():void
    {
        parent::tearDown();
    }    
}
