<?php

namespace guifcoelho\ImmuTable\Testing;

use guifcoelho\ImmuTable\Testing\Database;
use guifcoelho\ImmuTable\Testing\Support\DatabaseAssertions;

trait TestingDatabase {

    use DatabaseAssertions;

    protected $manager;

    public function __construct(){
        parent::__construct();
        $database = Database::boot();
        $this->manager = $database->getManager();
    }

}