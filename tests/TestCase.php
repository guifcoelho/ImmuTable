<?php

namespace guifcoelho\JsonModels\Tests;

use PHPUnit_Framework_TestCase as PHPUnit;


/**
 * Class TestCase
 *
 * @author  Mahmoud Zalt  <mahmoud@zalt.me>
 */
abstract class TestCase extends PHPUnit
{
    protected $factory_path = __DIR__."/Unit/SampleModels";

    public function __construct()
    {
        parent::__construct();
    }

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }    
}
