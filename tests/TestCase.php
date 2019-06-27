<?php

namespace guifcoelho\JsonModels\Tests;

use PHPUnit\Framework\TestCase as PHPUnit;


/**
 * Class TestCase
 *
 * @author  Mahmoud Zalt  <mahmoud@zalt.me>
 */
class TestCase extends PHPUnit
{
    protected $factory_path = __DIR__."/Unit/SampleModels/Factory";

    public function __construct()
    {
        parent::__construct();
    }

    public function setUp():void
    {
        parent::setUp();
    }

    public function tearDown():void
    {
        parent::tearDown();
    }    
}
