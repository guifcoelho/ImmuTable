<?php

return [

    /*
    |--------------------------------------------------------------------------
    |
    |--------------------------------------------------------------------------
    |
    */
    'path_to_tables' => function_exists('storage_path') ? storage_path('app/immutable/tables') : __DIR__.'/../storage/app/immutable/tables',
    'chunk_size' => 1024*10

];
