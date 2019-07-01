<?php

return [

    /*
    |--------------------------------------------------------------------------
    |
    |--------------------------------------------------------------------------
    |
    */
    'path_to_tables' => function_exists('storage_path') ? storage_path('app/jsontables') : __DIR__.'/../storage/app/jsontables',
    'chunk_size' => 1024

];
