<?php

namespace guifcoelho\ImmuTable\Testing;

use Illuminate\Database\Capsule\Manager as Capsule;
use guifcoelho\ImmuTable\Testing\Support\DatabaseAssertions;

trait TestingDatabase {

    use DatabaseAssertions;

    public $capsule;

    protected function bootDatabase(){
        $this->capsule = new Capsule;

        $this->capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'immutable',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'port'      => 3306
        ]);
        $this->capsule->bootEloquent();
    }

}