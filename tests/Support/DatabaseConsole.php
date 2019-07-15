<?php

require_once __DIR__."/../../vendor/autoload.php";

use guifcoelho\ImmuTable\Tests\Support\Database;

class ArgvInput{

    protected $command = '';

    public function __construct(array $argv = null){
        if (null === $argv) {
            $argv = $_SERVER['argv'];
        }
        $this->command = $argv[1];
    }

    public function getCommand():string
    {
        return $this->command;
    }
}

switch((new ArgvInput)->getCommand()){
    case 'migrate': Database::migrate(); break;
    case 'reset': Database::reset(); break;
    case 'migrate:fresh': Database::migrateFresh(); break;
    default: print("Command not known. Nothing happened.".PHP_EOL);
}