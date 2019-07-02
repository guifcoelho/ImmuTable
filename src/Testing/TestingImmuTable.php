<?php

namespace guifcoelho\ImmuTable\Testing;

use guifcoelho\ImmuTable\Model;
use guifcoelho\ImmuTable\Config;
use Symfony\Component\Finder\Finder;

trait TestingImmuTable
{
    protected function bootstrap():void{
        require __DIR__."/Support/Functions.php";
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
