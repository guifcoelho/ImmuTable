<?php

namespace guifcoelho\JsonModels\Testing;

use guifcoelho\JsonModels\Model;
use guifcoelho\JsonModels\Config;
use Symfony\Component\Finder\Finder;

trait TestingJsonModels
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

    protected function refreshJsonModels():void{
        $path = (new Config)->get('path_to_tables');
        if (is_dir($path)) {
            foreach (Finder::create()->files()->name('*.json')->in($path) as $file) {
                unlink($file->getRealPath());
            }
        }
    }
}
