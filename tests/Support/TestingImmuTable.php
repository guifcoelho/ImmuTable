<?php

namespace guifcoelho\ImmuTable\Tests\Support;

use guifcoelho\ImmuTable\Model;
use guifcoelho\ImmuTable\Config;
use Symfony\Component\Finder\Finder;

trait TestingImmuTable
{
    protected function bootstrap():void{
        require __DIR__."/../../src/Factory/Functions.php";
        $this->setTablesPath();
    }

    protected function setTablesPath():void
    {    
        $tables_path = (new Config)->get('path_to_tables');
        if(!file_exists($tables_path)){
            mkdir($tables_path, 0777, true);
        }
    }

    protected function refreshImmuTable():void{
        $path = (new Config)->get('path_to_tables');
        if (is_dir($path)) {
            foreach (Finder::create()->files()->name('*.ndjson')->in($path) as $file) {
                unlink($file->getRealPath());
            }
        }
    }
}
