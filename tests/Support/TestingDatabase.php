<?php

namespace guifcoelho\ImmuTable\Tests\Support;

use guifcoelho\ImmuTable\Tests\Support\Database;
use guifcoelho\ImmuTable\Tests\Support\Assert\DatabaseAssertions;

trait TestingDatabase {

    use DatabaseAssertions;

    protected $manager;

    public function __construct(){
        parent::__construct();
        $database = Database::boot();
        $this->manager = $database->getManager();
    }

}